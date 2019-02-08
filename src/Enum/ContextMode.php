<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Enum;

/**
 * Valid values for `vads_ctx_mode` request field.
 */
final class ContextMode
{
    public const TEST       = 'TEST';
    public const PRODUCTION = 'PRODUCTION';

    private function __construct()
    {
    }
}
