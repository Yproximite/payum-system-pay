<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests\Action;

use Payum\Core\Action\GetCurrencyAction;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Request\GetCurrency;
use Payum\Core\Tests\GenericActionTest;
use Payum\Core\Tests\Mocks\Entity\Payment;
use Yproximite\Payum\SystemPay\Action\ConvertPaymentAction;

class ConvertPaymentActionTest extends GenericActionTest
{
    protected $requestClass = Convert::class;

    protected $actionClass = ConvertPaymentAction::class;

    public function provideSupportedRequests()
    {
        yield [new $this->requestClass(new Payment(), 'array')];
        yield [new $this->requestClass($this->createMock(PaymentInterface::class), 'array')];
        yield [new $this->requestClass(new Payment(), 'array', $this->createMock('Payum\Core\Security\TokenInterface'))];
    }

    public function provideNotSupportedRequests()
    {
        yield ['foo'];
        yield [['foo']];
        yield [new \stdClass()];
        yield [$this->getMockForAbstractClass('Payum\Core\Request\Generic', [[]])];
        yield [new $this->requestClass(new \stdClass(), 'array')];
        yield [new $this->requestClass(new Payment(), 'foobar')];
        yield [new $this->requestClass($this->createMock(PaymentInterface::class), 'foobar')];
    }

    /**
     * @test
     */
    public function shouldCorrectlyConvertOrderToDetailsAndSetItBack()
    {
        $payment = new Payment();
        $payment->setNumber('354');
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount(123);
        $payment->setClientId('theClientId');
        $payment->setClientEmail('theClientEmail');

        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Payum\Core\Request\GetCurrency'))
            ->willReturnCallback(function (GetCurrency $request) {
                $action = new GetCurrencyAction();
                $action->execute($request);
            });

        $action = new ConvertPaymentAction();
        $action->setGateway($gatewayMock);

        $action->execute($convert = new Convert($payment, 'array'));

        $details = $convert->getResult();

        $this->assertNotEmpty($details);

        $this->assertArrayHasKey('vads_trans_id', $details);
        $this->assertSame('000354', $details['vads_trans_id']);

        $this->assertArrayHasKey('vads_trans_date', $details);
        $this->assertRegExp('#^20[0-9]{2}\d{2}\d{2}\d{2}\d{2}\d{2}$#', $details['vads_trans_date']);

        $this->assertArrayHasKey('vads_amount', $details);
        $this->assertSame(123, $details['vads_amount']);

        $this->assertArrayHasKey('vads_cust_id', $details);
        $this->assertSame('theClientId', $details['vads_cust_id']);

        $this->assertArrayHasKey('vads_cust_email', $details);
        $this->assertSame('theClientEmail', $details['vads_cust_email']);

        $this->assertArrayHasKey('vads_currency', $details);
        $this->assertSame('978', $details['vads_currency']);
    }
}
