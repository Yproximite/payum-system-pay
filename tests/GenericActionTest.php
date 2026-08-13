<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Generic;
use Payum\Core\Security\TokenInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Local copy of Payum\Core\Tests\GenericActionTest: payum/core marks its own
 * Tests directory as export-ignore, so the class is absent from the dist
 * archive Composer installs.
 */
abstract class GenericActionTest extends TestCase
{
    /**
     * @var class-string<Generic>
     */
    protected $requestClass;

    /**
     * @var class-string<ActionInterface>
     */
    protected $actionClass;

    /**
     * @var ActionInterface
     */
    protected $action;

    protected function setUp(): void
    {
        $this->action = new $this->actionClass();
    }

    public function provideSupportedRequests(): \Iterator
    {
        yield [new $this->requestClass([])];
        yield [new $this->requestClass(new \ArrayObject())];
    }

    public function provideNotSupportedRequests(): \Iterator
    {
        yield ['foo'];
        yield [['foo']];
        yield [new \stdClass()];
        yield [new $this->requestClass('foo')];
        yield [new $this->requestClass(new \stdClass())];
        yield [$this->getMockForAbstractClass(Generic::class, [[]])];
    }

    /**
     * @test
     */
    public function shouldImplementActionInterface()
    {
        $rc = new \ReflectionClass($this->actionClass);

        $this->assertTrue($rc->implementsInterface(ActionInterface::class));
    }

    /**
     * @test
     * @dataProvider provideSupportedRequests
     *
     * @param mixed $request
     */
    public function shouldSupportRequest($request)
    {
        $this->assertTrue($this->action->supports($request));
    }

    /**
     * @test
     * @dataProvider provideNotSupportedRequests
     *
     * @param mixed $request
     */
    public function shouldNotSupportRequest($request)
    {
        $this->assertFalse($this->action->supports($request));
    }

    /**
     * @test
     * @dataProvider provideNotSupportedRequests
     *
     * @param mixed $request
     */
    public function throwIfNotSupportedRequestGivenAsArgumentForExecute($request)
    {
        $this->expectException(RequestNotSupportedException::class);

        $this->action->execute($request);
    }

    /**
     * @return MockObject&GatewayInterface
     */
    protected function createGatewayMock()
    {
        return $this->createMock(GatewayInterface::class);
    }

    /**
     * @return MockObject&TokenInterface
     */
    protected function createTokenMock()
    {
        return $this->createMock(TokenInterface::class);
    }
}
