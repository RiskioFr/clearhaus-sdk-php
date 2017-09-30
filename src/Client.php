<?php

namespace Clearhaus;

use Clearhaus\HttpClient\Builder;
use Clearhaus\HttpClient\Message\ResponseMediator;
use Clearhaus\HttpClient\Plugin\Authentication;
use Http\Client\Common\Plugin;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\Authentication\BasicAuth;

class Client
{
    const LIVE_ENDPOINT = 'https://gateway.clearhaus.com';
    const TEST_ENDPOINT = 'https://gateway.test.clearhaus.com';

    private $builder;

    public function __construct(Builder $builder = null, bool $test = false)
    {
        $this->builder = $builder ?: new Builder();

        $uri = $test ? self::TEST_ENDPOINT : self::LIVE_ENDPOINT;

        $builder->addPlugin(new Plugin\AddHostPlugin(
            UriFactoryDiscovery::find()->createUri($uri)
        ));
    }

    public function setApiKey(string $apiKey)
    {
        $this->builder->removePlugin(Authentication::class);

        $authenticationPlugin = new Authentication(new BasicAuth($apiKey, ''));
        $this->builder->addPlugin($authenticationPlugin);
    }

    public function authorize(array $params) : array
    {
        return $this->post('/authorizations', $params);
    }

    private function post(string $path, array $params, array $headers = []) : array
    {
        $httpClient = $this->builder->build();

        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $response = $httpClient->post($path, $headers, $params);

        return ResponseMediator::getContent($response);
    }
}
