<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay;

use Yproximite\Payum\SystemPay\Action\AuthorizeAction;
use Yproximite\Payum\SystemPay\Action\CancelAction;
use Yproximite\Payum\SystemPay\Action\ConvertPaymentAction;
use Yproximite\Payum\SystemPay\Action\CaptureAction;
use Yproximite\Payum\SystemPay\Action\NotifyAction;
use Yproximite\Payum\SystemPay\Action\RefundAction;
use Yproximite\Payum\SystemPay\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class SkeletonGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name'           => 'sp_plus',
            'payum.factory_title'          => 'sp_plus',
            'payum.action.capture'         => new CaptureAction(),
            'payum.action.authorize'       => new AuthorizeAction(),
            'payum.action.refund'          => new RefundAction(),
            'payum.action.cancel'          => new CancelAction(),
            'payum.action.notify'          => new NotifyAction(),
            'payum.action.status'          => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false === $config['payum.api']) {
            $config['payum.default_options'] = [
                'sandbox' => true,
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
