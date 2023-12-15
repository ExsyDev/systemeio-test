<?php

namespace App\Tests;

use App\PaymentProcessor\PaypalPaymentProcessor;
use App\PaymentProcessor\StripePaymentProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\Controller\PaymentAdapter;

class PaymentTest extends TestCase
{
    /**
     * @return void
     */
    public function testPaypalSuccess(): void
    {
        [$paypalMock, $stripeMock] = $this->createMocks();

        $paymentAdapter = $this->getPaymentAdapter($paypalMock, $stripeMock);

        $result = $paymentAdapter->paypal(100);
        $this->assertTrue($result, 'PayPal payment is successful.');
    }

    /**
     * @return void
     */
    public function testPaypalError(): void
    {
        [$paypalMock, $stripeMock] = $this->createMocks();

        $paypalMock
            ->expects($this->once())
            ->method('pay')
            ->willThrowException(new \Exception('High price'));

        $paymentAdapter = $this->getPaymentAdapter($paypalMock, $stripeMock);

        $result = $paymentAdapter->paypal(200);
        $this->assertEquals('High price', $result);
    }

    /**
     * @return void
     */
    public function testStripeSuccess(): void
    {
        [$paypalMock, $stripeMock] = $this->createMocks();

        $stripeMock
            ->expects($this->once())
            ->method('processPayment')
            ->willReturn(true);

        $paymentAdapter = $this->getPaymentAdapter($paypalMock, $stripeMock);

        $result = $paymentAdapter->stripe(100);
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testStripeError(): void
    {
        [$paypalMock, $stripeMock] = $this->createMocks();

        $stripeMock
            ->expects($this->once())
            ->method('processPayment')
            ->willReturn(false);

        $paymentAdapter = $this->getPaymentAdapter($paypalMock, $stripeMock);

        $result = $paymentAdapter->stripe(5);
        $this->assertEquals('price < than 10', $result);
    }

    /**
     * @return array
     */
    private function createMocks(): array
    {
        return [$this->createPaypalMock(), $this->createStripeMock()];
    }

    /**
     * @param PaypalPaymentProcessor $paypal
     * @param StripePaymentProcessor $stripe
     * @return PaymentAdapter
     */
    private function getPaymentAdapter(
        PaypalPaymentProcessor $paypal,
        StripePaymentProcessor $stripe
    ): PaymentAdapter
    {
        return new PaymentAdapter($paypal, $stripe);
    }

    /**
     * @return MockObject
     */
    private function createPaypalMock(): MockObject
    {
        return
            $this->getMockBuilder(PaypalPaymentProcessor::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['pay'])
                ->getMock();
    }

    /**
     * @return MockObject
     */
    private function createStripeMock(): MockObject
    {
        return
            $this->getMockBuilder(StripePaymentProcessor::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['processPayment'])
                ->getMock();
    }
}
