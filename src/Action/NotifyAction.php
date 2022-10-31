<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Yproximite\Payum\SystemPay\Action\Api\BaseApiAwareAction;
use Yproximite\Payum\SystemPay\Api;

class NotifyAction extends BaseApiAwareAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Notify $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        $parameters = $httpRequest->request;
        $signature = $this->api->getSignature($parameters);
        if (!array_key_exists('signature', $parameters) || $parameters['signature'] !== $signature) {
            // Dirty hack : when you are notified for a nth payment, system pay does not send the payment configuration but payum adds it from the details in bdd.
            if (array_key_exists(Api::FIELD_VADS_PAYMENT_CONFIG, $parameters)) {
                unset($parameters[Api::FIELD_VADS_PAYMENT_CONFIG]);
                $signature = $this->api->getSignature($parameters);
                if ($parameters['signature'] !== $signature) {
                    throw new HttpResponse('Bad signature', 403);
                }
            } else {
                throw new HttpResponse('Bad signature', 403);
            }
        }

        $model->replace($httpRequest->request);

        throw new HttpResponse('OK', 200);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
