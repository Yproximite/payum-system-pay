# Payum System Pay

> A Payum gateway to use SystemPay (French payment system)

[![Build Status](https://travis-ci.com/Yproximite/payum-system-pay.svg?token=pNBs2oaRpfxdyhqWf28h&branch=master)](https://travis-ci.com/Yproximite/payum-system-pay)

## Requirements

- PHP 7.2+
- [Payum](https://github.com/Payum/Payum)
- Optionally [PayumBundle](https://github.com/Payum/PayumBundle) and Symfony 3 or 4+

## Installation

```bash
$ composer require yproximite/payum-system-pay
```

## Configuration

### With PayumBundle (Symfony)

First register the gateway factory in your services definition:
```yaml
# config/services.yaml or app/config/services.yml
services:
    yproximite.system_pay_gateway_factory:
        class: Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder
        arguments: [Yproximite\Payum\SystemPay\SystemPayGatewayFactory]
        tags:
            - { name: payum.gateway_factory_builder, factory: system_pay }
```

Then configure the gateway:

```yaml
# config/packages/payum.yaml or app/config/config.yml

payum:
  gateways:
    system_pay:
      factory: system_pay
      vads_site_id: 'change it' # required 
      certif_prod: 'change it' # required 
      certif_test: 'change it' # required 
      sandbox: true

```

### With Payum

```php
<?php
//config.php

use Payum\Core\PayumBuilder;
use Payum\Core\Payum;

/** @var Payum $payum */
$payum = (new PayumBuilder())
    ->addDefaultStorages()

    ->addGateway('gatewayName', [
        'factory' => 'system_may',
        'vads_site_id' => 'change it',
        'certif_prod'  => 'change it',
        'certif_test'  => 'change it',
        'sandbox'      => true,
    ])

    ->getPayum()
;
```
