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
use Yproximite\Payum\SystemPay\Enum\ActionMode;
use Yproximite\Payum\SystemPay\Enum\PageAction;
use Yproximite\Payum\SystemPay\Enum\PaymentConfig;
use Yproximite\Payum\SystemPay\Enum\Version;

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
                'sandbox'             => true,
                'vads_site_id'        => null,
                'vads_action_mode'    => ActionMode::INTERACTIVE,
                'vads_page_action'    => PageAction::PAYMENT,
                'vads_payment_config' => PaymentConfig::SINGLE,
                'vads_version'        => Version::V2,
                'certif_test'         => null,
                'certif_prod'         => null,
                'url_notif_ok'        => null,
                'url_notif_ko'        => null,
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'sandbox',
                'vads_site_id',
                'vads_action_mode',
                'vads_page_action',
                'vads_payment_config',
                'certif_test',
                'certif_prod',
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, new SignatureGenerator(), $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
