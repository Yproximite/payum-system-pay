<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests\Action;

use Http\Message\MessageFactory;
use Payum\Core\HttpClientInterface;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Yproximite\Payum\SystemPay\Action\NotifyAction;
use Yproximite\Payum\SystemPay\Api;
use Yproximite\Payum\SystemPay\SignatureAlgorithm;
use Yproximite\Payum\SystemPay\SignatureGenerator;
use Yproximite\Payum\SystemPay\Tests\GenericActionTest;

class NotifyActionTest extends GenericActionTest
{
    /**
     * The certificate the notification data below is signed with.
     */
    private const CERTIFICATE = '1122334455667788';

    protected $requestClass = Notify::class;

    protected $actionClass = NotifyAction::class;

    /**
     * @test
     */
    public function shouldUpdateModelIfNotificationValid()
    {
        $action = $this->createActionWithNotificationData($this->provideNotificationData());

        $model = new \ArrayObject([
            'vads_action_mode' => 'INTERACTIVE',
            'vads_amount'      => '123',
        ]);

        try {
            $action->execute(new Notify($model));
        } catch (HttpResponse $reply) {
            $this->assertEquals($this->provideNotificationData(), (array) $model);

            $this->assertSame(200, $reply->getStatusCode());
            $this->assertSame('OK', $reply->getContent());

            return;
        }

        $this->fail('The exception is expected');
    }

    /**
     * @test
     */
    public function shouldNotUpdateModelIfSignatureInvalid()
    {
        $data              = $this->provideNotificationData();
        $data['signature'] = 'anInvalidSignature';

        $action = $this->createActionWithNotificationData($data);

        $model = new \ArrayObject([
            'vads_action_mode' => 'INTERACTIVE',
            'vads_amount'      => '123',
        ]);

        try {
            $action->execute(new Notify($model));
        } catch (HttpResponse $reply) {
            $this->assertEquals([
                'vads_action_mode' => 'INTERACTIVE',
                'vads_amount'      => '123',
            ], (array) $model);

            $this->assertSame(403, $reply->getStatusCode());
            $this->assertSame('Bad signature', $reply->getContent());

            return;
        }

        $this->fail('The exception is expected');
    }

    private function createActionWithNotificationData(array $data): NotifyAction
    {
        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Payum\Core\Request\GetHttpRequest'))
            ->will($this->returnCallback(function (GetHttpRequest $request) use ($data) {
                $request->request = $data;
            }));

        $action = new NotifyAction();
        $action->setGateway($gatewayMock);
        $action->setApi(new Api([
            'sandbox'        => true,
            'certif_test'    => self::CERTIFICATE,
            'certif_prod'    => 'theProdCertificate',
            'hash_algorithm' => SignatureAlgorithm::toPayumOption(SignatureAlgorithm::SHA1),
        ], new SignatureGenerator(), $this->createMock(HttpClientInterface::class), $this->createMock(MessageFactory::class)));

        return $action;
    }

    protected function provideNotificationData(): array
    {
        $data = [
            'vads_action_mode'             => 'INTERACTIVE',
            'vads_amount'                  => '123',
            'vads_auth_mode'               => 'FULL',
            'vads_auth_number'             => '3fdda3',
            'vads_auth_result'             => '00',
            'vads_bank_label'              => "Banque de démo et de l'innovation",
            'vads_brand_management'        => '{"userChoice":false,"brandList":"CB | VISA_ELECTRON","brand":"CB"}',
            'vads_capture_delay'           => '0',
            'vads_card_brand'              => 'CB',
            'vads_card_country'            => 'FR',
            'vads_card_number'             => '491748XXXXXX0008',
            'vads_contract_used'           => '5709355',
            'vads_ctx_mode'                => 'TEST',
            'vads_currency'                => '978',
            'vads_cust_email'              => 'foo@example.com',
            'vads_cust_id'                 => 'anId',
            'vads_effective_amount'        => '123',
            'vads_effective_creation_date' => '20190314150031',
            'vads_effective_currency'      => '978',
            'vads_expiry_month'            => '6',
            'vads_expiry_year'             => '2020',
            'vads_extra_result'            => '',
            'vads_hash'                    => 'ce72ac995b8337d59b6fc3080d06428eacff2c5f16cbf40344d7d37ad6e48a7c',
            'vads_language'                => 'fr',
            'vads_operation_type'          => 'DEBIT',
            'vads_page_action'             => 'PAYMENT',
            'vads_payment_certificate'     => 'b095547eb9250969deb447f56a78757b41a779d1',
            'vads_payment_config'          => 'SINGLE',
            'vads_payment_src'             => 'EC',
            'vads_pays_ip'                 => 'FR',
            'vads_presentation_date'       => '20190314150031',
            'vads_result'                  => '00',
            'vads_sequence_number'         => '1',
            'vads_site_id'                 => '44442461',
            'vads_threeds_cavv'            => 'Q2F2dkNhdnZDYXZ2Q2F2dkNhdnY=',
            'vads_threeds_cavvAlgorithm'   => '2',
            'vads_threeds_eci'             => '05',
            'vads_threeds_enrolled'        => 'Y',
            'vads_threeds_error_code'      => '',
            'vads_threeds_exit_status'     => '10',
            'vads_threeds_sign_valid'      => '1',
            'vads_threeds_status'          => 'Y',
            'vads_threeds_xid'             => 'elRHOHJLQ1pVRUkxc1NXVGVIUzQ=',
            'vads_trans_date'              => '20190314150040',
            'vads_trans_id'                => '000004',
            'vads_trans_status'            => 'AUTHORISED',
            'vads_trans_uuid'              => 'd0292f525c8d4a7c9b181cd113b473bc',
            'vads_url_check_src'           => 'PAY',
            'vads_validation_mode'         => '0',
            'vads_version'                 => 'V2',
            'vads_warranty_result'         => 'YES',
        ];

        return ['signature' => (new SignatureGenerator())->generate($data, self::CERTIFICATE)] + $data;
    }
}
