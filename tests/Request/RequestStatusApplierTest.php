<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests\Request;

use Payum\Core\Request\GetStatusInterface;
use PHPUnit\Framework\TestCase;
use Yproximite\Payum\SystemPay\Request\RequestStatusApplier;

class RequestStatusApplierTest extends TestCase
{
    /**
     * @dataProvider provideApplyStatus
     */
    public function testApplyStatus(?string $status, string $expectedMethodName)
    {
        $requestStatusApplier = new RequestStatusApplier();
        $requestStatusApplier->apply($status, $this->createRequestMock($expectedMethodName));
    }

    public function provideApplyStatus()
    {
        yield [null, 'markNew'];
        yield ['qsdqsd', 'markUnknown'];
        yield ['ABANDONED', 'markCanceled'];
        yield ['AUTHORISED', 'markAuthorized'];
        yield ['AUTHORISED_TO_VALIDATE', 'markPending'];
        yield ['CANCELLED', 'markCanceled'];
        yield ['CAPTURED', 'markCaptured'];
        yield ['CAPTURE_FAILED', 'markFailed'];
        yield ['EXPIRED', 'markExpired'];
        yield ['INITIAL', 'markNew'];
        yield ['NOT_CREATED', 'markUnknown'];
        yield ['REFUSED', 'markCanceled'];
        yield ['SUSPENDED', 'markSuspended'];
        yield ['UNDER_VERIFICATION', 'markPending'];
        yield ['WAITING_AUTHORISATION', 'markPending'];
        yield ['WAITING_AUTHORISATION_TO_VALIDATE', 'markPending'];
    }

    protected function createRequestMock(?string $methodName = null): GetStatusInterface
    {
        $request = $this->createMock(GetStatusInterface::class);

        if (null !== $methodName) {
            $request
                ->expects($this->once())
                ->method($methodName);
        }

        return $request;
    }
}
