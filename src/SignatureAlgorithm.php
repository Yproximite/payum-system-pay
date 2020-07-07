<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay;

class SignatureAlgorithm
{
    public const SHA1        = 'sha1';
    public const HMAC_SHA256 = 'hmac-sha256';
    public const ALL         = [
        self::SHA1,
        self::HMAC_SHA256,
    ];

    // changing this value = breaking change
    protected const HASH_ALGORITHM_PREFIX = 'algo-';

    public static function toPayumOption(string $algo): string
    {
        if (self::HASH_ALGORITHM_PREFIX === substr($algo, 0, $length = strlen(self::HASH_ALGORITHM_PREFIX))) {
            throw new \InvalidArgumentException(sprintf('The prefix "%s" is already present in passed algorithm "%s".', self::HASH_ALGORITHM_PREFIX, $algo));
        }

        if (!in_array($algo, self::ALL, true)) {
            throw new \InvalidArgumentException(sprintf('Unknown algorithm "%s".', $algo));
        }

        $algo = self::HASH_ALGORITHM_PREFIX.$algo;

        return $algo;
    }

    public static function fromPayumOption(string $algo): string
    {
        // workaround for https://github.com/Payum/Payum/issues/692
        if (self::HASH_ALGORITHM_PREFIX === substr($algo, 0, $length = strlen(self::HASH_ALGORITHM_PREFIX))) {
            $algo = substr($algo, $length);

            if (!in_array($algo, self::ALL, true)) {
                throw new \InvalidArgumentException(sprintf('Unknown algorithm "%s".', $algo));
            }

            return $algo;
        }

        throw new \InvalidArgumentException(sprintf('The prefix "%s" is not present in passed algorithm "%s".', self::HASH_ALGORITHM_PREFIX, $algo));
    }
}
