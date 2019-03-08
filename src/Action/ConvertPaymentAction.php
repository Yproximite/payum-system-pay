<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Request\GetCurrency;
use Yproximite\Payum\SystemPay\Api;

class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        $details[Api::FIELD_VADS_TRANS_ID]   = sprintf('%06d', $payment->getNumber());
        $details[Api::FIELD_VADS_TRANS_DATE] = gmdate('YmdHis');
        $details[Api::FIELD_VADS_AMOUNT]     = $payment->getTotalAmount();

        $this->gateway->execute($currency = new GetCurrency($payment->getCurrencyCode()));
        $details[Api::FIELD_VADS_CURRENCY] = $currency->numeric;

        $request->setResult((array) $details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            'array' === $request->getTo();
    }
}
