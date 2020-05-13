<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay\Tests;

use Payum\Core\Bridge\Guzzle\HttpClientFactory;
use Payum\Core\Bridge\Spl\ArrayObject;
use Yproximite\Payum\SystemPay\SystemPayGatewayFactory;

class SystemPayGatewayFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function shouldSubClassGatewayFactory()
    {
        $rc = new \ReflectionClass('Yproximite\Payum\SystemPay\SystemPayGatewayFactory');

        $this->assertTrue($rc->isSubclassOf('Payum\Core\GatewayFactory'));
    }

    /**
     * @test
     * @expectedException \Payum\Core\Exception\LogicException
     * @expectedExceptionMessage The vads_site_id, certif_test, certif_prod fields are required.
     */
    public function shouldThrowIfRequiredOptionsAreNotPassed()
    {
        $factory = new SystemPayGatewayFactory();

        $factory->create();
    }

    /**
     * @test
     */
    public function testDefaultOptions()
    {
        $factory = new SystemPayGatewayFactory();

        $config = $factory->createConfig();

        $this->assertSame('system_pay', $config['payum.factory_name']);
        $this->assertSame('system_pay', $config['payum.factory_title']);

        $this->assertInstanceOf('Yproximite\Payum\SystemPay\Request\RequestStatusApplier', $config['payum.request_status_applier']);

        $this->assertInstanceOf('Yproximite\Payum\SystemPay\Action\CaptureAction', $config['payum.action.capture']);
        $this->assertInstanceOf('Yproximite\Payum\SystemPay\Action\NotifyAction', $config['payum.action.notify']);
        $this->assertInstanceOf('Yproximite\Payum\SystemPay\Action\StatusAction', $config['payum.action.status'](ArrayObject::ensureArrayObject($config)));
        $this->assertInstanceOf('Yproximite\Payum\SystemPay\Action\ConvertPaymentAction', $config['payum.action.convert_payment']);

        $this->assertNull($config['payum.default_options']['vads_site_id']);
        $this->assertSame('INTERACTIVE', $config['payum.default_options']['vads_action_mode']);
        $this->assertSame('PAYMENT', $config['payum.default_options']['vads_page_action']);
        $this->assertSame('SINGLE', $config['payum.default_options']['vads_payment_config']);
        $this->assertSame('V2', $config['payum.default_options']['vads_version']);
        $this->assertTrue($config['payum.default_options']['sandbox']);
        $this->assertNull($config['payum.default_options']['certif_prod']);
        $this->assertNull($config['payum.default_options']['certif_test']);
        $this->assertEquals('algo-sha1', $config['payum.default_options']['hash_algorithm']);
        $this->assertEquals([
            'vads_site_id',
            'vads_action_mode',
            'vads_page_action',
            'certif_test',
            'certif_prod',
        ], $config['payum.required_options']);

        $this->assertInstanceOf(\Closure::class, $config['payum.api']);
    }
}
