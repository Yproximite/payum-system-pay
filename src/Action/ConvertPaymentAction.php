<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Request\GetCurrency;
use Yproximite\Payum\SystemPay\Enum\RequestParam;

class ConvertPaymentAction implements ActionInterface
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

        $details[RequestParam::VADS_TRANS_ID]   = $payment->getNumber();
        $details[RequestParam::VADS_TRANS_DATE] = gmdate('YmdHis');
        $details[RequestParam::VADS_AMOUNT]     = $payment->getTotalAmount();

        $this->gateway->execute($currency = new GetCurrency($payment->getCurrencyCode()));
        $details[RequestParam::VADS_CURRENCY] = $currency->numeric;

        dump($details);
        $request->setResult((array) $details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        dump($request, $request->getSource(), $request->getTo());

        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            'array' === $request->getTo();
    }
}
