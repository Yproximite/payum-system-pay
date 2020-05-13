<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay;

class SignatureGenerator
{
    public function generate(array $fields, string $certificate, $hashAlgorithm = 'sha1'): string
    {
        // Filter keys
        $fields = array_filter($fields, function ($key) {
            return 'vads_' === substr($key, 0, 5);
        }, ARRAY_FILTER_USE_KEY);

        // Sort them alphabetically
        ksort($fields);

        // Push certificate at the end
        $fields[] = $certificate;

        // Join fields values
        $str = implode('+', array_values($fields));

        if ($hashAlgorithm === 'sha1') {
            return sha1($str);
        }

        return base64_encode(hash_hmac('sha256', $str, $certificate, true));
    }
}
