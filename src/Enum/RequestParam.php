<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Enum;

class RequestParam
{
    public const VADS_SITE_ID        = 'vads_site_id';
    public const VADS_CTX_MODE       = 'vads_ctx_mode';
    public const VADS_TRANS_ID       = 'vads_trans_id';
    public const VADS_TRANS_DATE     = 'vads_trans_date';
    public const VADS_AMOUNT         = 'vads_amount';
    public const VADS_CURRENCY       = 'vads_currency';
    public const VADS_ACTION_MODE    = 'vads_action_mode';
    public const VADS_PAGE_ACTION    = 'vads_page_action';
    public const VADS_PAYMENT_CONFIG = 'vads_payment_config';
    public const VADS_VERSION        = 'vads_version';
    public const VADS_URL_CHECK      = 'vads_url_check';

    private function __construct()
    {
    }
}
