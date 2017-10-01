<?php

namespace Clearhaus;

use Clearhaus\Exception\MissingArgumentException;
use Clearhaus\HttpClient\Builder;
use Clearhaus\HttpClient\Message\ResponseMediator;
use Clearhaus\HttpClient\Plugin\Authentication;
use Clearhaus\HttpClient\Plugin\ClearhausExceptionThrower;
use Http\Client\Common\Plugin;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\Authentication\BasicAuth;

class Client
{
    const ENDPOINT_LIVE = 'https://gateway.clearhaus.com';
    const ENDPOINT_TEST = 'https://gateway.test.clearhaus.com';

    const MODE_LIVE = 'live';
    const MODE_TEST = 'test';

    const CONTENT_TYPE = 'application/vnd.clearhaus-gateway.hal+json';

    private $builder;

    public function __construct(Builder $builder = null, string $mode = self::MODE_TEST)
    {
        $this->builder = $builder ?: new Builder();

        $uri = $mode === self::MODE_TEST ? self::ENDPOINT_TEST : self::ENDPOINT_LIVE;

        $builder->addPlugin(new ClearhausExceptionThrower());
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
        if (!isset($params['amount'], $params['currency'], $params['ip'], $params['card'])) {
            throw new MissingArgumentException(['amount', 'currency', 'ip', 'card']);
        }

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
