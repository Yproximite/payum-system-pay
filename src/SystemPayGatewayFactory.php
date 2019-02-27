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
use Yproximite\Payum\SystemPay\Enum\RequestParam;
use Yproximite\Payum\SystemPay\Enum\Version;
use Yproximite\Payum\SystemPay\Request\RequestStatusApplier;

class SystemPayGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name'           => 'system_pay',
            'payum.factory_title'          => 'system_pay',
            'payum.action.capture'         => new CaptureAction(),
            'payum.action.authorize'       => new AuthorizeAction(),
            'payum.action.refund'          => new RefundAction(),
            'payum.action.cancel'          => new CancelAction(),
            'payum.action.notify'          => new NotifyAction(),
            'payum.action.status'          => function (ArrayObject $config) {
                return new StatusAction($config['payum.request_status_applier']);
            },
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.request_status_applier' => new RequestStatusApplier(),
        ]);

        if (false === ($config['payum.api'] ?? false)) {
            $config['payum.default_options'] = [
                RequestParam::VADS_SITE_ID        => null,
                RequestParam::VADS_ACTION_MODE    => ActionMode::INTERACTIVE,
                RequestParam::VADS_PAGE_ACTION    => PageAction::PAYMENT,
                RequestParam::VADS_PAYMENT_CONFIG => PaymentConfig::SINGLE,
                RequestParam::VADS_VERSION        => Version::V2,
                'sandbox'                         => null,
                'certif_prod'                     => null,
                'certif_test'                     => null,
                'url_notif_ok'                    => null,
                'url_notif_ko'                    => null,
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                RequestParam::VADS_SITE_ID,
                RequestParam::VADS_ACTION_MODE,
                RequestParam::VADS_PAGE_ACTION,
                'sandbox',
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
