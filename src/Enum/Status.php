<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Enum;

/**
 * Valid values for `vads_trans_status` request field.
 */
final class Status
{
    public const ABANDONED                         = 'ABANDONED';
    public const AUTHORISED                        = 'AUTHORISED';
    public const AUTHORISED_TO_VALIDATE            = 'AUTHORISED_TO_VALIDATE';
    public const CANCELLED                         = 'CANCELLED';
    public const CAPTURED                          = 'CAPTURED';
    public const CAPTURE_FAILED                    = 'CAPTURE_FAILED';
    public const EXPIRED                           = 'EXPIRED';
    public const INITIAL                           = 'INITIAL';
    public const NOT_CREATED                       = 'NOT_CREATED';
    public const REFUSED                           = 'REFUSED';
    public const SUSPENDED                         = 'SUSPENDED';
    public const UNDER_VERIFICATION                = 'UNDER_VERIFICATION';
    public const WAITING_AUTHORISATION             = 'WAITING_AUTHORISATION';
    public const WAITING_AUTHORISATION_TO_VALIDATE = 'WAITING_AUTHORISATION_TO_VALIDATE';

    private function __construct()
    {
    }
}
