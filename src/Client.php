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
        if (!isset($params['amount'], $params['currency'], $params['card'])) {
            throw new MissingArgumentException(['amount', 'currency', 'card']);
        }

        return $this->post('/authorizations', $params);
    }

    public function authorizeFromCardId(string $cardId, array $params) : array
    {
        if (!isset($params['amount'], $params['currency'])) {
            throw new MissingArgumentException(['amount', 'currency']);
        }

        return $this->post(sprintf('/cards/%s/authorizations', $cardId), $params);
    }

    public function getAuthorization($id) : array
    {
        return $this->get(sprintf('/authorizations/%s', $id));
    }

    public function capture(string $authorizationId, array $params = []) : array
    {
        return $this->post(sprintf('/authorizations/%s/captures', $authorizationId), $params);
    }

    public function getCapture($id) : array
    {
        return $this->get(sprintf('/captures/%s', $id));
    }

    public function refund(string $authorizationId, array $params = []) : array
    {
        return $this->post(sprintf('/authorizations/%s/refunds', $authorizationId), $params);
    }

    public function getRefund($id) : array
    {
        return $this->get(sprintf('/refunds/%s', $id));
    }

    public function void(string $authorizationId, array $params = []) : array
    {
        return $this->post(sprintf('/authorizations/%s/voids', $authorizationId), $params);
    }

    public function getVoid($id) : array
    {
        return $this->get(sprintf('/voids/%s', $id));
    }

    public function credit(string $cardId, array $params = []) : array
    {
        if (!isset($params['amount'], $params['currency'])) {
            throw new MissingArgumentException(['amount', 'currency']);
        }

        return $this->post(sprintf('/cards/%s/credits', $cardId), $params);
    }

    public function getCredit($id) : array
    {
        return $this->get(sprintf('/credits/%s', $id));
    }

    public function createCard(array $params = []) : array
    {
        if (!isset($params['number'], $params['expire_month'], $params['expire_year'], $params['csc'])) {
            throw new MissingArgumentException(['number', 'expire_month', 'expire_year', 'csc']);
        }

        return $this->post('/cards', $params);
    }

    public function getCard($id) : array
    {
        return $this->get(sprintf('/cards/%s', $id));
    }

    public function getAccount() : array
    {
        return $this->get('/accounts');
    }

    private function post(string $path, array $params, array $headers = []) : array
    {
        $httpClient = $this->builder->build();

        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $response = $httpClient->post($path, $headers, $params);

        return ResponseMediator::getContent($response);
    }

    private function get(string $path, array $headers = []) : array
    {
        $httpClient = $this->builder->build();

        $response = $httpClient->get($path, $headers);

        return ResponseMediator::getContent($response);
    }
}
