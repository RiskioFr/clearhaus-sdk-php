Clearhaus PHP SDK
=================

SDK for Clearhaus API written in PHP and decoupled from any HTTP messaging client using HTTPlug.

[![Build Status](https://img.shields.io/travis/RiskioFr/clearhaus-sdk-php.svg?style=flat)](http://travis-ci.org/RiskioFr/clearhaus-sdk-php)
[![Latest Stable Version](http://img.shields.io/packagist/v/riskio/clearhaus-sdk-php.svg?style=flat)](https://packagist.org/packages/riskio/clearhaus-sdk-php)
[![Total Downloads](http://img.shields.io/packagist/dt/riskio/clearhaus-sdk-php.svg?style=flat)](https://packagist.org/packages/riskio/clearhaus-sdk-php)

## Requirements

* PHP 7.0+
* [php-http/client-common ^1.3](https://github.com/php-http/client-common)
* [php-http/discovery ^1.10](https://github.com/php-http/discovery)
* [php-http/httplug ^1.0](https://github.com/php-http/httplug)

## Installation

Clearhaus SDK only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

You can install the module from command line:

```sh
$ composer require clearhaus/sdk
```

## Usage

```php
use Clearhaus\Client;

$client = new Client();
$client->setApiKey($apiKey);

$client->authorize([
    'amount' => 2050,
    'currency' => 'EUR',
    'ip' => '1.1.1.1',
    'card' => [
        'number' => '4111111111111111',
        'expire_month' => '06',
        'expire_year' => '2018',
        'csc' => '123',
    ],
]);
```

## Testing

``` bash
$ vendor/bin/phpspec run
```

## Credits

- [Nicolas Eeckeloo](https://github.com/neeckeloo)
- [All Contributors](https://github.com/RiskioFr/clearhaus-sdk-php/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/RiskioFr/clearhaus-sdk-php/blob/master/LICENSE) for more information.
