<?php

declare(strict_types=1);

namespace App\Controller;

use App\PaymentProcessor\PaypalPaymentProcessor;
use App\PaymentProcessor\StripePaymentProcessor;
use App\Enum\PaymentProcessorEnum;
use Exception;

class PaymentAdapter
{
    /**
     * @param PaypalPaymentProcessor $paypal
     * @param StripePaymentProcessor $stripe
     */
    public function __construct(
        private readonly PaypalPaymentProcessor $paypal,
        private readonly StripePaymentProcessor $stripe
    )
    {

    }

    /**
     * @param string $name
     * @param int $price
     * @return bool|string
     */
    public function pay(string $name, int $price): bool|string
    {
        return
            match ($name) {
                PaymentProcessorEnum::PAYPAL => $this->paypal($price),
                PaymentProcessorEnum::STRIPE => $this->stripe($price),
                default => (new Exception(' payment processor does not exists'))->getMessage()
            };
    }

    /**
     * @param int $price
     * @return bool|string
     */
    public function paypal(int $price): bool|string
    {
        try {
            return $this->paypal->pay($price) === null;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param int $price
     * @return bool|string
     */
    public function stripe(int $price): bool|string
    {
        try {
            if ($this->stripe->processPayment($price)) {
                return true;
            }

            return 'price < than 10';
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}