<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Enum;

/**
 * Valid values for `vads_page_action` request field.
 */
final class PageAction
{
    public const PAYMENT = 'PAYMENT';

    private function __construct()
    {
    }
}
