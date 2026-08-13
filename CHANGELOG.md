# Changelog

## 3.0.0

### BREAKING CHANGES

- PHP 8.1+ is now required. Composer refuses by default to install packages under a security advisory, and the first advisory-free Twig — a hard requirement of `payum/core` — is 3.27.0, which needs PHP >= 8.1 ([#28](https://github.com/Yproximite/payum-system-pay/pull/28))
- `payum/core` moved from `require-dev` to `require`, where it belongs for a library, and is now constrained to `^1.7` ([#28](https://github.com/Yproximite/payum-system-pay/pull/28))

## 2.0.0

### BREAKING CHANGES

- Public constant `Yproximite\Payum\SystemPay\SignatureGenerator::HASH_ALGORITHM_PREFIX` does not exist anymore, use `Yproximite\Payum\SystemPaym\SignatureAlgorithm::toPayumOption(string $algo)` instead.

## 1.1.0

### Features

- Add HMAC-SHA-256 hash algorithm support ([#20](https://github.com/Yproximite/payum-system-pay/pull/20))

## 1.0.2

### Fixes

- Fix "sandbox" default option ([#16](https://github.com/Yproximite/payum-system-pay/pull/16))

## 1.0.1

### Fixes

- Fix redirection when cancelling payment ([#15](https://github.com/Yproximite/payum-system-pay/pull/15))

## 1.0.0

Initial release! :tada:
