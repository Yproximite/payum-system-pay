<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests\Action;

use Payum\Core\GatewayInterface;
use Payum\Core\Model\Token;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Yproximite\Payum\SystemPay\Action\CaptureAction;
use PHPUnit\Framework\TestCase;
use Yproximite\Payum\SystemPay\Api;

class CaptureActionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldImplementActionInterface()
    {
        $rc = new \ReflectionClass('Yproximite\Payum\SystemPay\Action\CaptureAction');

        $this->assertTrue($rc->implementsInterface('Payum\Core\Action\ActionInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new CaptureAction();

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function shouldSupportCaptureWithArrayAccessAsModel()
    {
        $action = new CaptureAction();

        $request = new Capture($this->createMock('ArrayAccess'));

        $this->assertTrue($action->supports($request));
    }

    /**
     * @test
     */
    public function shouldNotSupportNotCapture()
    {
        $action = new CaptureAction();

        $request = new \stdClass();

        $this->assertFalse($action->supports($request));
    }

    /**
     * @test
     */
    public function shouldNotSupportCaptureAndNotArrayAccessAsModel()
    {
        $action = new CaptureAction();

        $request = new Capture(new \stdClass());

        $this->assertFalse($action->supports($request));
    }

    /**
     * @test
     *
     * @expectedException \Payum\Core\Exception\RequestNotSupportedException
     */
    public function throwIfNotSupportedRequestGivenAsArgumentForExecute()
    {
        $action = new CaptureAction();

        $action->execute(new \stdClass());
    }

    /**
     * @test
     */
    public function shouldDoNothingIfPaymentHasResult()
    {
        $model = [
            'vads_result' => Api::STATUS_CAPTURED,
        ];

        $apiMock = $this->createApiMock();
        $apiMock
            ->expects($this->never())
            ->method('doPayment');

        $action = new CaptureAction();
        $action->setApi($apiMock);

        $action->execute(new Capture($model));
    }

    /**
     * @test
     */
    public function shouldGenerateNotifyTokenIfNoOneIsPassed()
    {
        $model = new \ArrayObject([]);

        $captureToken = new Token();
        $captureToken->setGatewayName('theGatewayName');
        $captureToken->setTargetUrl('theReturnUrl');
        $captureToken->setDetails($model);

        $notifyToken = new Token();
        $notifyToken->setTargetUrl('theNotifyUrl');

        $tokenFactoryMock = $this->createMock(GenericTokenFactoryInterface::class);
        $tokenFactoryMock
            ->expects($this->once())
            ->method('createNotifyToken')
            ->with('theGatewayName', $model)
            ->will($this->returnValue($notifyToken));

        $apiMock = $this->createApiMock();
        $apiMock
            ->expects($this->once())
            ->method('doPayment')
            ->with([
                'vads_url_return' => 'theReturnUrl',
                'vads_url_check'  => 'theNotifyUrl',
            ]);

        $action = new CaptureAction();
        $action->setApi($apiMock);
        $action->setGenericTokenFactory($tokenFactoryMock);

        $request = new Capture($captureToken);
        $request->setModel($model);

        $action->execute($request);

        $this->assertArrayHasKey('vads_url_return', $model);
        $this->assertEquals('theReturnUrl', $model['vads_url_return']);

        $this->assertArrayHasKey('vads_url_check', $model);
        $this->assertEquals('theNotifyUrl', $model['vads_url_check']);
    }

    /**
     * @return MockObject|GatewayInterface
     */
    protected function createGatewayMock()
    {
        return $this->createMock(GatewayInterface::class);
    }

    /**
     * @return MockObject|Api
     */
    protected function createApiMock()
    {
        return $this->createMock(Api::class);
    }
}
