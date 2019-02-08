<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Enum;

/**
 * Valid values for `vads_payment_config` request field.
 */
final class PaymentConfig
{
    public const SINGLE = 'SINGLE';
    public const MULTI  = 'MULTI';

    private function __construct()
    {
    }
}
