<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests;

use PHPUnit\Framework\TestCase;
use Yproximite\Payum\SystemPay\SignatureGenerator;

class SignatureGeneratorTest extends TestCase
{
    /**
     * @dataProvider testGenerateDataProvider
     */
    public function testGenerate(string $expectedSignature, string $certificate, array $fields)
    {
        $signatureGenerator = new SignatureGenerator();

        $signature = $signatureGenerator->generate($fields, $certificate);

        $this->assertEquals($expectedSignature, $signature);
    }

    public function testGenerateDataProvider()
    {
        // https://www.ocl.natixis.com/systempay/public/uploads/fichier/Guide_d_implementation_formulaire_paiement_Systempay_v3.2018122018144810.pdf
        yield [
            'expectedSignature' => '59c96b34c74b9375c332b0b6a32e6deeec87de2b',
            'certificate'       => '1122334455667788',
            'fields'            => [
                'vads_action_mode'    => 'INTERACTIVE',
                'vads_amount'         => 5124,
                'vads_ctx_mode'       => 'TEST',
                'vads_currency'       => 978,
                'vads_page_action'    => 'PAYMENT',
                'vads_payment_config' => 'SINGLE',
                'vads_site_id'        => 12345678,
                'vads_trans_date'     => 20170129130025,
                'vads_trans_id'       => 123456,
                'vads_version'        => 'V2',
            ],
        ];
    }
}
