<?php

declare(strict_types=1);

namespace App\Controller;

use Exception;
use Throwable;
use App\Entity\Products;
use App\Entity\Taxes;
use App\Entity\Coupons;
use App\Repository\ProductsRepository;
use App\Repository\TaxesRepository;
use App\Repository\CouponsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\FormInterface;
use App\Forms\CalculationType;
use App\Forms\PaymentType;
use App\Enum\PaymentCouponTypeEnum;
use App\Loggers\CalculationLogger;
use App\Loggers\PaymentLogger;

class ApiController extends AbstractController
{
    const TRANS_SYSTEM_PRODUCT_NOT_FOUND = 'system.product.not_found';
    const TRANS_SYSTEM_TAX_NOT_FOUND = 'system.tax.not_found';
    const TRANS_SYSTEM_COUPON_NOT_FOUND = 'system.coupon.not_found';

    const TRANS_CALCULATION_SUCCESS = 'api.calculation.success';
    const TRANS_CALCULATION_ERROR = 'api.calculation.error';

    const TRANS_PAYMENT_SUCCESS = 'api.payment.success';
    const TRANS_PAYMENT_ERROR = 'api.payment.error';

    /**
     * @param TranslatorInterface $translator
     * @param PaymentAdapter $paymentAdapter
     * @param ProductsRepository $productsRepository
     * @param TaxesRepository $taxesRepository
     * @param CouponsRepository $couponsRepository
     * @param CalculationLogger $calculationLogger
     * @param PaymentLogger $paymentLogger
     */
    public function __construct(
        public readonly TranslatorInterface $translator,
        public readonly PaymentAdapter $paymentAdapter,
        public readonly ProductsRepository $productsRepository,
        public readonly TaxesRepository $taxesRepository,
        public readonly CouponsRepository $couponsRepository,
        protected readonly CalculationLogger $calculationLogger,
        protected readonly PaymentLogger $paymentLogger
    ) {
        //
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function calculation(Request $request): JsonResponse
    {
        try {
            $this->calculationLogger->info(json_encode($request->getPayload()->all()));

            $form = $this->createForm(CalculationType::class);
            $form->submit(json_decode($request->getContent(), true));

            if (!$form->isValid()) {
                $errors = $this->getFormErrors($form);

                $this->calculationLogger->info(json_encode($errors));

                return $this->getErrorsResponse(
                    $this->translator->trans(self::TRANS_CALCULATION_ERROR),
                    $errors
                );
            }

            $product = $this->getProduct($form->get('product')->getData());

            $tax = $this->getTax($form->get('taxNumber')->getData());

            $orderPrice = $product->getPriceEuro() * $form->get('count')->getData();
            $couponCode = $form->get('couponCode')->getData();

            if ($couponCode) {
                $coupon = $this->getCoupon($couponCode);
                $couponDiscount = $coupon->getValue();
                $discountPrice =
                    match ($coupon->getType()) {
                        PaymentCouponTypeEnum::FIXED => $couponDiscount,
                        PaymentCouponTypeEnum::PERCENT => ($couponDiscount / 100) * $orderPrice
                    };

                $orderPrice = $orderPrice - $discountPrice;
            }

            $orderTax = ($tax->getPercent() / 100) * $orderPrice;
            $result = number_format($orderPrice + $orderTax, 2);

            $response = [
                'message' => $this->translator->trans(self::TRANS_CALCULATION_SUCCESS),
                'result_euro' => $result
            ];

            // log response
            $this->calculationLogger->info(json_encode($response));

            return $this->json(
                $response,
                Response::HTTP_OK
            );
        } catch (\Throwable $e) {
            $error = $e->getMessage();

            $this->calculationLogger->info($error);

            return $this->getErrorsResponse(
                $this->translator->trans(self::TRANS_CALCULATION_ERROR),
                [$error]
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function payment(Request $request): JsonResponse
    {
        try {
            $this->paymentLogger->info(json_encode($request->getPayload()->all()));

            $form = $this->createForm(PaymentType::class);
            $form->submit(json_decode($request->getContent(), true));

            if (!$form->isValid()) {
                $errors = $this->getFormErrors($form);

                $this->paymentLogger->info(json_encode($errors));

                return $this->getErrorsResponse(
                    $this->translator->trans(self::TRANS_PAYMENT_ERROR),
                    $errors
                );
            }

            $name = $form->get('paymentProcessor')->getData();
            $price = (int) $form->get('price')->getData();

            $result = $this->paymentAdapter->pay($name, $price);

            if (is_bool($result) && $result) {
                $response =  [
                    'message' => $this->translator->trans(self::TRANS_PAYMENT_SUCCESS),
                    'price' => $price
                ];

                $this->paymentLogger->info(json_encode($response));

                return $this->json(
                    $response,
                    Response::HTTP_OK
                );
            }

            throw new Exception($result);
        } catch (Throwable $e) {
            $error = $e->getMessage();

            $this->paymentLogger->info($error);

            return $this->getErrorsResponse(
                $this->translator->trans(self::TRANS_PAYMENT_ERROR),
                [$error]
            );
        }
    }

    /**
     * @param string $id
     * @return Products
     * @throws Exception
     */
    private function getProduct(string $id): Products
    {
        $product = $this->productsRepository->find($id);

        if (is_null($product)) {
            throw new Exception($this->translator->trans(self::TRANS_SYSTEM_PRODUCT_NOT_FOUND));
        }

        return $product;
    }

    /**
     * @param string $taxNumber
     * @return Taxes
     * @throws Exception
     */
    private function getTax(string $taxNumber): Taxes
    {
        $tax = $this->taxesRepository->findOneBy(['tax_number' => $taxNumber]);

        if (is_null($tax)) {
            throw new Exception($this->translator->trans(self::TRANS_SYSTEM_TAX_NOT_FOUND));
        }

        return $tax;
    }

    /**
     * @param string $couponCode
     * @return Coupons
     * @throws Exception
     */
    protected function getCoupon(string $couponCode): Coupons
    {
        $coupon = $this->couponsRepository->findOneBy(['code' => $couponCode]);

        if (is_null($coupon)) {
            throw new Exception($this->translator->trans(self::TRANS_SYSTEM_COUPON_NOT_FOUND));
        }

        return $coupon;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    protected function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @param string $message
     * @param array $errors
     * @return JsonResponse
     */
    protected function getErrorsResponse(string $message, array $errors): JsonResponse
    {
        return $this->json(
            [
                'message' => $message,
                'errors' => $errors
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
