<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\TokenInterface;
use Yproximite\Payum\SystemPay\Action\Api\BaseApiAwareAction;
use Yproximite\Payum\SystemPay\Api;

class CaptureAction extends BaseApiAwareAction implements ActionInterface, GatewayAwareInterface, GenericTokenFactoryAwareInterface
{
    use GatewayAwareTrait;
    use GenericTokenFactoryAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        if (null !== $details[Api::FIELD_VADS_RESULT]) {
            return;
        }

        if (null === $details[Api::FIELD_VADS_URL_CHECK] && $request->getToken() instanceof TokenInterface) {
            $notifyToken = $this->tokenFactory->createNotifyToken(
                $request->getToken()->getGatewayName(),
                $request->getToken()->getDetails()
            );

            $details[Api::FIELD_VADS_URL_CHECK] = $notifyToken->getTargetUrl();
        }

        if (null === $details[Api::FIELD_VADS_URL_CANCEL] && $request->getToken() instanceof TokenInterface) {
            // We are directly redirecting the user to the "done" page if he cancels payment.
            // Because when he clicks on the button "Retourner à la boutique", SystemPay does not send us back
            // data (POST or GET) to tell us the user canceled his payment.
            //
            // That means the user was redirected to the "capture" page:
            //  - which calls `ConvertPaymentAction`, but there is no data to use (like a field `vads_cancelled`), so the payment is like a NEW payment
            //  - which calls this `CaptureAction`, and since there is no `Api::FIELD_VADS_RESULT` field defined, it executes `$this->api->doPayment()` again
            //
            // So it's bad, and we should trick the "done" page to display an error if the payment is new.
            $details[Api::FIELD_VADS_URL_CANCEL] = $request->getToken()->getAfterUrl();
        }

        $details[Api::FIELD_VADS_URL_RETURN] = $request->getToken()->getTargetUrl();

        $this->api->doPayment((array) $details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
