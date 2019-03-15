<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests\Action;

use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Tests\GenericActionTest;
use Yproximite\Payum\SystemPay\Action\StatusAction;
use Yproximite\Payum\SystemPay\Request\RequestStatusApplier;

class StatusActionTest extends GenericActionTest
{
    protected $requestClass = GetHumanStatus::class;
    protected $actionClass  = StatusAction::class;

    protected function setUp()
    {
        $this->action = new $this->actionClass(new RequestStatusApplier());
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        // override
    }

    /**
     * @test
     * @dataProvider provideMarkRequest
     */
    public function shouldMarkRequest(?string $status, string $expectedRequestStatus)
    {
        $model = new \ArrayObject([
            'vads_trans_status' => $status,
        ]);

        $this->action->execute($request = new GetHumanStatus($model));

        $this->assertEquals($expectedRequestStatus, $request->getValue());
    }

    public function provideMarkRequest()
    {
        yield [null, 'new'];
        yield ['qsdqsd', 'unknown'];
        yield ['ABANDONED', 'canceled'];
        yield ['AUTHORISED', 'authorized'];
        yield ['AUTHORISED_TO_VALIDATE', 'pending'];
        yield ['CANCELLED', 'canceled'];
        yield ['CAPTURED', 'captured'];
        yield ['CAPTURE_FAILED', 'failed'];
        yield ['EXPIRED', 'expired'];
        yield ['INITIAL', 'new'];
        yield ['NOT_CREATED', 'unknown'];
        yield ['REFUSED', 'canceled'];
        yield ['SUSPENDED', 'suspended'];
        yield ['UNDER_VERIFICATION', 'pending'];
        yield ['WAITING_AUTHORISATION', 'pending'];
        yield ['WAITING_AUTHORISATION_TO_VALIDATE', 'pending'];
    }
}
