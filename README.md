Clearhaus PHP SDK
=================

SDK for Clearhaus API written in PHP and decoupled from any HTTP messaging client using HTTPlug.

[![Build Status](https://img.shields.io/travis/RiskioFr/clearhaus-sdk-php.svg?style=flat)](http://travis-ci.org/RiskioFr/clearhaus-sdk-php)
[![Latest Stable Version](http://img.shields.io/packagist/v/riskio/clearhaus-sdk-php.svg?style=flat)](https://packagist.org/packages/riskio/clearhaus-sdk-php)
[![Total Downloads](http://img.shields.io/packagist/dt/riskio/clearhaus-sdk-php.svg?style=flat)](https://packagist.org/packages/riskio/clearhaus-sdk-php)

## Requirements

* PHP 7.0+
* [php-http/httplug ^1.1](https://github.com/php-http/httplug)

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
