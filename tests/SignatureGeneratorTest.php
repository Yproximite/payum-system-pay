<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests;

use PHPUnit\Framework\TestCase;
use Yproximite\Payum\SystemPay\SignatureGenerator;

class SignatureGeneratorTest extends TestCase
{
    /**
     * @dataProvider provideTestGenerate
     */
    public function testGenerate(string $expectedSignature, string $certificate, string $hashAlgorithm, array $fields)
    {
        $signatureGenerator = new SignatureGenerator();

        $signature = $signatureGenerator->generate($fields, $certificate, $hashAlgorithm);

        $this->assertEquals($expectedSignature, $signature);
    }

    public function provideTestGenerate()
    {
        // https://www.ocl.natixis.com/systempay/public/uploads/fichier/Guide_d_implementation_formulaire_paiement_Systempay_v3.2018122018144810.pdf
        yield [
            'expectedSignature' => '59c96b34c74b9375c332b0b6a32e6deeec87de2b',
            'certificate'       => '1122334455667788',
            'hashAlgorithm'     => 'sha1',
            'fields'            => [
                'vads_action_mode'    => 'INTERACTIVE',
                'vads_amount'         => '5124',
                'vads_ctx_mode'       => 'TEST',
                'vads_currency'       => '978',
                'vads_page_action'    => 'PAYMENT',
                'vads_payment_config' => 'SINGLE',
                'vads_site_id'        => '12345678',
                'vads_trans_date'     => '20170129130025',
                'vads_trans_id'       => '123456',
                'vads_version'        => 'V2',
            ],
        ];

        yield 'hmac-sha256' => [
            'expectedSignature' => 'f8B2q09tV5R2aSVFkscjGMVemd56VUJsSxtErQZQaQs=',
            'certificate'       => '1122334455667788',
            'hashAlgorithm'     => 'hmac-sha256',
            'fields'            => [
                'vads_trans_id'       => '000001',
                'vads_trans_date'     => '20200513114252',
                'vads_amount'         => '10',
                'vads_cust_id'        => '',
                'vads_cust_email'     => 'test@example.com',
                'vads_currency'       => '978',
                'vads_url_check'      => 'http://my-app.vm:8000/paiement/notify?payum_token=51qV9QqdpT6KUdswfVBeHT6fNSQY9PF5znyzHGeQwYA',
                'vads_url_cancel'     => 'http://my-app.vm:8000/paiement/done?payum_token=hN5XJXj5sp-VZKYzsAHgbP5rbm9rHS7OVLtAgDD19oM',
                'vads_url_return'     => 'http://my-app.vm:8000/payment/capture/rQD3r-35NKHJHOCea6kLqt3768dZt2ZhWOwbzNpLvV4',
                'vads_ctx_mode'       => 'TEST',
                'vads_site_id'        => '12345678',
                'vads_action_mode'    => 'INTERACTIVE',
                'vads_page_action'    => 'PAYMENT',
                'vads_payment_config' => 'SINGLE',
                'vads_version'        => 'V2',
            ],
        ];
    }
}
