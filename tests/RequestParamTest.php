<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests;

use PHPUnit\Framework\TestCase;
use Yproximite\Payum\SystemPay\Enum\RequestParam;

class RequestParamTest extends TestCase
{
    /**
     * @dataProvider provideTestName
     */
    public function testName(string $expectedName, string $name)
    {
        $this->assertEquals($expectedName, $name);
    }

    public function provideTestName()
    {
        yield ['vads_site_id', RequestParam::VADS_SITE_ID];
        yield ['vads_ctx_mode', RequestParam::VADS_CTX_MODE];
        yield ['vads_trans_id', RequestParam::VADS_TRANS_ID];
        yield ['vads_trans_date', RequestParam::VADS_TRANS_DATE];
        yield ['vads_amount', RequestParam::VADS_AMOUNT];
        yield ['vads_currency', RequestParam::VADS_CURRENCY];
        yield ['vads_action_mode', RequestParam::VADS_ACTION_MODE];
        yield ['vads_page_action', RequestParam::VADS_PAGE_ACTION];
        yield ['vads_payment_config', RequestParam::VADS_PAYMENT_CONFIG];
        yield ['vads_version', RequestParam::VADS_VERSION];
    }
}
