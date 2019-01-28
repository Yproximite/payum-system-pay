<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Yproximite\Payum\SystemPay\Api;

abstract class BaseApiAwareAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait;
    use ApiAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }
}
