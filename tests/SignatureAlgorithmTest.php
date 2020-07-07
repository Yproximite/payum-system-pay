<?php

namespace Yproximite\Payum\SystemPay\Tests;

use PHPUnit\Framework\TestCase;
use Yproximite\Payum\SystemPay\SignatureAlgorithm;

class SignatureAlgorithmTest extends TestCase
{
    public function testAlgos(): void
    {
        static::assertEquals('sha1', SignatureAlgorithm::SHA1);
        static::assertEquals('hmac-sha256', SignatureAlgorithm::HMAC_SHA256);
        static::assertEquals([SignatureAlgorithm::SHA1, SignatureAlgorithm::HMAC_SHA256], SignatureAlgorithm::ALL);
    }

    public function testToPayumOptionSuccess(): void
    {
        static::assertEquals('algo-sha1', SignatureAlgorithm::toPayumOption(SignatureAlgorithm::SHA1));
        static::assertEquals('algo-hmac-sha256', SignatureAlgorithm::toPayumOption(SignatureAlgorithm::HMAC_SHA256));
    }

    public function testToPayumOptionFailAlreadyPrefixedAlgo(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The prefix "algo-" is already present in passed algorithm "algo-sha1".');

        SignatureAlgorithm::toPayumOption('algo-'.SignatureAlgorithm::SHA1);
    }

    public function testToPayumOptionFailWithUnknownAlgo(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown algorithm "foobar".');

        SignatureAlgorithm::toPayumOption('foobar');
    }

    public function testFromPayumOptionSuccess(): void
    {
        static::assertEquals(SignatureAlgorithm::SHA1, SignatureAlgorithm::fromPayumOption('algo-sha1'));
        static::assertEquals(SignatureAlgorithm::HMAC_SHA256, SignatureAlgorithm::fromPayumOption('algo-hmac-sha256'));
    }

    public function testFromPayumOptionFailAlreadyPrefixedAlgo(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The prefix "algo-" is not present in passed algorithm "sha1".');

        SignatureAlgorithm::fromPayumOption(SignatureAlgorithm::SHA1);
    }

    public function testFromPayumOptionFailWithUnknownAlgo(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown algorithm "foobar".');

        SignatureAlgorithm::fromPayumOption('algo-foobar');
    }
}
