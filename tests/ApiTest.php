<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests;

use Http\Message\MessageFactory;
use Payum\Core\HttpClientInterface;
use Payum\Core\Reply\HttpPostRedirect;
use PHPUnit\Framework\TestCase;
use Yproximite\Payum\SystemPay\Api;
use Yproximite\Payum\SystemPay\SignatureGenerator;

class ApiTest extends TestCase
{
    /**
     * @dataProvider provideConstants
     */
    public function testConstants(string $constantValue, string $expectedValue)
    {
        $this->assertSame($expectedValue, $constantValue);
    }

    public function provideConstants()
    {
        yield [Api::V2, 'V2'];
        yield [Api::ACTION_MODE_INTERACTIVE, 'INTERACTIVE'];
        yield [Api::CONTEXT_MODE_TEST, 'TEST'];
        yield [Api::CONTEXT_MODE_PRODUCTION, 'PRODUCTION'];
        yield [Api::PAGE_ACTION_PAYMENT, 'PAYMENT'];
        yield [Api::PAYMENT_CONFIG_SINGLE, 'SINGLE'];
        yield [Api::FIELD_VADS_SITE_ID, 'vads_site_id'];
        yield [Api::FIELD_VADS_CTX_MODE, 'vads_ctx_mode'];
        yield [Api::FIELD_VADS_TRANS_ID, 'vads_trans_id'];
        yield [Api::FIELD_VADS_TRANS_DATE, 'vads_trans_date'];
        yield [Api::FIELD_VADS_TRANS_STATUS, 'vads_trans_status'];
        yield [Api::FIELD_VADS_AMOUNT, 'vads_amount'];
        yield [Api::FIELD_VADS_CURRENCY, 'vads_currency'];
        yield [Api::FIELD_VADS_SEQUENCE_NUMBER, 'vads_sequence_number'];
        yield [Api::FIELD_VADS_ACTION_MODE, 'vads_action_mode'];
        yield [Api::FIELD_VADS_PAGE_ACTION, 'vads_page_action'];
        yield [Api::FIELD_VADS_PAYMENT_CONFIG, 'vads_payment_config'];
        yield [Api::FIELD_VADS_VERSION, 'vads_version'];
        yield [Api::FIELD_VADS_URL_RETURN, 'vads_url_return'];
        yield [Api::FIELD_VADS_URL_CHECK, 'vads_url_check'];
        yield [Api::FIELD_VADS_URL_CHECK_SRC, 'vads_url_check_src'];
        yield [Api::FIELD_VADS_RESULT, 'vads_result'];
        yield [Api::FIELD_VADS_CUSTOMER_ID, 'vads_cust_id'];
        yield [Api::FIELD_VADS_CUSTOMER_EMAIL, 'vads_cust_email'];
        yield [Api::STATUS_ABANDONED, 'ABANDONED'];
        yield [Api::STATUS_AUTHORISED, 'AUTHORISED'];
        yield [Api::STATUS_AUTHORISED_TO_VALIDATE, 'AUTHORISED_TO_VALIDATE'];
        yield [Api::STATUS_CANCELLED, 'CANCELLED'];
        yield [Api::STATUS_CAPTURED, 'CAPTURED'];
        yield [Api::STATUS_CAPTURE_FAILED, 'CAPTURE_FAILED'];
        yield [Api::STATUS_EXPIRED, 'EXPIRED'];
        yield [Api::STATUS_INITIAL, 'INITIAL'];
        yield [Api::STATUS_NOT_CREATED, 'NOT_CREATED'];
        yield [Api::STATUS_REFUSED, 'REFUSED'];
        yield [Api::STATUS_SUSPENDED, 'SUSPENDED'];
        yield [Api::STATUS_UNDER_VERIFICATION, 'UNDER_VERIFICATION'];
        yield [Api::STATUS_WAITING_AUTHORISATION, 'WAITING_AUTHORISATION'];
        yield [Api::STATUS_WAITING_AUTHORISATION_TO_VALIDATE, 'WAITING_AUTHORISATION_TO_VALIDATE'];
    }

    public function testGetAlgorithmHash(): void
    {
        $this->expectException(HttpPostRedirect::class);

        $signatureGenerator = $this->createMock(SignatureGenerator::class);
        $signatureGenerator->method('generate')
            ->with($this->isType('array'), $this->equalTo('the test certificate'), $this->equalTo('sha1'))
            ->willReturn('<signature>');

        $httpClient     = $this->createMock(HttpClientInterface::class);
        $messageFactory = $this->createMock(MessageFactory::class);

        $options = [
            'sandbox'        => true,
            'certif_test'    => 'the test certificate',
            'certif_prod'    => 'the prod certificate',
            'hash_algorithm' => 'algo-sha1',
        ];

        $api = new Api($options, $signatureGenerator, $httpClient, $messageFactory);

        $api->doPayment([]);
    }
}
