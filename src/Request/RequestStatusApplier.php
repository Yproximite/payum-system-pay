<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Request;

use Payum\Core\Request\GetStatusInterface as Request;
use Yproximite\Payum\SystemPay\Enum\Status;

class RequestStatusApplier
{
    /** @var array<string, callable<Request>> */
    protected $appliers = [];

    public function __construct()
    {
        $this->appliers[Status::ABANDONED]                         = function (Request $request) { $request->markCanceled(); };
        $this->appliers[Status::AUTHORISED]                        = function (Request $request) { $request->markAuthorized(); };
        $this->appliers[Status::AUTHORISED_TO_VALIDATE]            = function (Request $request) { $request->markPending(); };
        $this->appliers[Status::CANCELLED]                         = function (Request $request) { $request->markCanceled(); };
        $this->appliers[Status::CAPTURED]                          = function (Request $request) { $request->markCaptured(); };
        $this->appliers[Status::CAPTURE_FAILED]                    = function (Request $request) { $request->markFailed(); };
        $this->appliers[Status::EXPIRED]                           = function (Request $request) { $request->markExpired(); };
        $this->appliers[Status::INITIAL]                           = function (Request $request) { $request->markNew(); };
        $this->appliers[Status::NOT_CREATED]                       = function (Request $request) { $request->markUnknown(); };
        $this->appliers[Status::REFUSED]                           = function (Request $request) { $request->markCanceled(); };
        $this->appliers[Status::SUSPENDED]                         = function (Request $request) { $request->markSuspended(); };
        $this->appliers[Status::UNDER_VERIFICATION]                = function (Request $request) { $request->markPending(); };
        $this->appliers[Status::WAITING_AUTHORISATION]             = function (Request $request) { $request->markPending(); };
        $this->appliers[Status::WAITING_AUTHORISATION_TO_VALIDATE] = function (Request $request) { $request->markPending(); };
    }

    public function apply(?string $status, Request $request): void
    {
        if (null === $status) {
            $request->markNew();

            return;
        }

        if (!array_key_exists($status, $this->appliers)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown status "%s", valid status are: "%s".',
                $status,
                implode('", "', array_keys($this->appliers))
            ));
        }

        $this->appliers[$status]($request);
    }
}
