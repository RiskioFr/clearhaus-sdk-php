Clearhaus PHP SDK
=================

SDK for Clearhaus API written in PHP and decoupled from any HTTP messaging client using HTTPlug.

You can sign up for a Clearhaus account at https://www.clearhaus.com/.

[![Build Status](https://img.shields.io/travis/RiskioFr/clearhaus-sdk-php.svg?style=flat-square)](http://travis-ci.org/RiskioFr/clearhaus-sdk-php)
[![Latest Stable Version](http://img.shields.io/packagist/v/clearhaus/sdk.svg?style=flat-square)](https://packagist.org/packages/clearhaus/sdk)
[![Total Downloads](http://img.shields.io/packagist/dt/clearhaus/sdk.svg?style=flat-square)](https://packagist.org/packages/clearhaus/sdk)
[![GitHub license](https://img.shields.io/github/license/RiskioFr/clearhaus-sdk-php.svg?style=flat-square)](https://github.com/RiskioFr/clearhaus-sdk-php/blob/master/LICENSE)

## Requirements

* PHP 7.0+
* [php-http/client-common ^1.3](https://github.com/php-http/client-common)
* [php-http/discovery ^1.0](https://github.com/php-http/discovery)
* [php-http/httplug ^1.0](https://github.com/php-http/httplug)

## Installation

Clearhaus SDK only officially supports installation through Composer. For Composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

You can install the module from command line:

```sh
$ composer require clearhaus/sdk
```

## Documentation

Please see http://docs.gateway.clearhaus.com for up-to-date documentation.

### Authentication

For authentication, you must provide an API key that you will find in your account:

```php
use Clearhaus\Client;

$client = new Client($apiKey);
```

### Signed requests

The signature is an RSA signature of the HTTP body; it is represented in Hex. The signee must be identified by the signing API-key.

```php
use Clearhaus\Client;

$client->enableSignature();
```

### Authorizations

To reserve money on a cardholder’s bank account you make a new authorization resource.

```php
$authorization = $client->authorizations->authorize([
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

You can also use a card previously tokenized.

```php
$authorization = $client->authorizations->authorizeFromCardId($cardId, [
    'amount' => 2050,
    'currency' => 'EUR',
    'ip' => '1.1.1.1',
]);
```

### Captures

To transfer money from a cardholder’s bank account to your merchant bank account you make a new capture resource. You can make multiple captures for an authorization transaction.

```php
$client->captures->capture($authorization['id']);
```

You can withdraw a partial amount by providing an amount parameter:

```php
$client->captures->capture($authorization['id'], ['amount' => 1000]);
```

### Refunds

To refund money to a cardholder’s bank account you make a new refund resource. You can make multiple refunds for an authorization transaction.

```php
$client->refunds->refund($authorization['id']);
```

You can refund a partial amount by providing an amount parameter:

```php
$client->refunds->refund($authorization['id'], ['amount' => 500]);
```

### Voids

To release reserved money on a cardholder’s bank account you make a new void resource. A reservation normally last for 7 days depending on issuing bank and is then automatically released.

```php
$client->voids->void($authorization['id']);
```

### Credits

To payout (e.g. winnings and not refunds) money to a cardholder’s bank account you make a new credit resource. You must have a card resource to make a credit transaction.

```php
$client->credits->credit($card['id'], [
    'amount' => 2050,
    'currency' => 'EUR',
]);
```

### Cards

A card resource (token) corresponds to a payment card and can be used to make a credit or authorization transaction without providing sensitive card data. A card resource must be used to make subsequent recurring authorization transactions.

```php
$card = $client->cards->createCard([
    'card' => [
        'number' => '4111111111111111',
        'expire_month' => '06',
        'expire_year' => '2018',
        'csc' => '123',
    ],
]);
```

### Accounts

The account resource holds basic merchant account information.

```php
$account = $client->accounts->getAccount();
```

### 3-D Secure

3-D Secure is a protocol designed to improve security for online transactions. Before you continue please read more about this protocol at [3Dsecure.io](http://docs.3dsecure.io/).

To perform a 3-D Secure transaction you make an ordinary authorization including a pares value:

```php
$authorization = $client->authorizations->authorize([
    'amount' => 2050,
    'currency' => 'EUR',
    'ip' => '1.1.1.1',
    'card' => [
        'number' => '4111111111111111',
        'expire_month' => '06',
        'expire_year' => '2018',
        'csc' => '123',
    ],
    'threed_secure' => [
        'pares' => '<some-pares-value>',
    ],
]);
```

## PSR-11 factory

You can use the predefined factory `Clearhaus\Container\ClientFactory` to instantiate a Clearhaus client:

```php
use Clearhaus\Container\ClientFactory;

$factory = new ClientFactory();
$client = $factory($psrContainer);
```

The client configuration must look like below:

```php
use Clearhaus\Client;

return [
    'clearhaus_sdk' => [
        'api_key' => null, // Allow to provide API key that you will find in your account
        'mode' => Client::MODE_TEST, // Allow to define the usage of either test or live accounts
        'use_signature' => true, // Allow to configure the usage of request signature
        'plugins' => [], // HTTPlug plugins that allow to add some processing logic
    ],
];
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
