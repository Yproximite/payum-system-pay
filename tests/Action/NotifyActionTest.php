<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests\Action;

use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Payum\Core\Tests\GenericActionTest;
use Yproximite\Payum\SystemPay\Action\NotifyAction;

class NotifyActionTest extends GenericActionTest
{
    protected $requestClass = Notify::class;

    protected $actionClass = NotifyAction::class;

    /**
     * @test
     */
    public function shouldUpdateModelIfNotificationValid()
    {
        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf('Payum\Core\Request\GetHttpRequest'))
            ->will($this->returnCallback(function (GetHttpRequest $request) {
                $request->request = $this->provideNotificationData();
            }));

        $action = new NotifyAction();
        $action->setGateway($gatewayMock);

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

    protected function provideNotificationData(): array
    {
        return [
            'signature'                    => '833d20ce6b2c27dd054c289f7ba22a26aaf3350d',
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
    }
}
