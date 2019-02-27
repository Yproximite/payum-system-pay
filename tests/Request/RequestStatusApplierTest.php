<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests\Request;

use Payum\Core\Request\GetStatusInterface;
use PHPUnit\Framework\TestCase;
use Yproximite\Payum\SystemPay\Request\RequestStatusApplier;

class RequestStatusApplierTest extends TestCase
{
    public function testApplyNullStatus()
    {
        $requestStatusApplier = new RequestStatusApplier();
        $requestStatusApplier->apply(null, $this->createRequestMock('markNew'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown status "qsd", valid status are: "ABANDONED", "AUTHORISED", "AUTHORISED_TO_VALIDATE", "CANCELLED", "CAPTURED", "CAPTURE_FAILED", "EXPIRED", "INITIAL", "NOT_CREATED", "REFUSED", "SUSPENDED", "UNDER_VERIFICATION", "WAITING_AUTHORISATION", "WAITING_AUTHORISATION_TO_VALIDATE".
     */
    public function testApplyUnknownStatus()
    {
        $requestStatusApplier = new RequestStatusApplier();
        $requestStatusApplier->apply('qsd', $this->createRequestMock());
    }

    /**
     * @dataProvider provideApplyValidStatus
     */
    public function testApplyValidStatus(string $status, string $expectedMethodName)
    {
        $requestStatusApplier = new RequestStatusApplier();
        $requestStatusApplier->apply($status, $this->createRequestMock($expectedMethodName));
    }

    public function provideApplyValidStatus()
    {
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
