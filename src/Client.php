<?php

namespace Clearhaus;

use Clearhaus\Api;
use Clearhaus\Exception\BadMethodCallException;
use Clearhaus\Exception\InvalidArgumentException;
use Clearhaus\HttpClient\Builder;
use Clearhaus\HttpClient\Plugin\Authentication;
use Clearhaus\HttpClient\Plugin\ClearhausExceptionThrower;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\Authentication\BasicAuth;

/**
 * @method Api\Accounts accounts()
 * @method Api\Authorizations authorizations()
 * @method Api\Captures captures()
 * @method Api\Cards cards()
 * @method Api\Credits credits()
 * @method Api\Refunds refunds()
 * @method Api\Voids voids()
 */
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

    public function getHttpClient() : HttpMethodsClient
    {
        return $this->builder->build();
    }

    public function setApiKey(string $apiKey)
    {
        $this->builder->removePlugin(Authentication::class);

        $authenticationPlugin = new Authentication(new BasicAuth($apiKey, ''));
        $this->builder->addPlugin($authenticationPlugin);
    }

    public function __call($name, $args) : Api\AbstractApi
    {
        try {
            return $this->api($name);
        } catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(sprintf('Undefined method called: "%s"', $name));
        }
    }

    public function api($name) : Api\AbstractApi
    {
        switch ($name) {
            case 'accounts':
                return new Api\Accounts($this);
            case 'authorizations':
                return new Api\Authorizations($this);
            case 'captures':
                return new Api\Captures($this);
            case 'cards':
                return new Api\Cards($this);
            case 'credits':
                return new Api\Credits($this);
            case 'refunds':
                return new Api\Refunds($this);
            case 'voids':
                return new Api\Voids($this);
            default:
                throw new InvalidArgumentException(
                    sprintf('Undefined api instance called: "%s"', $name)
                );
        }
    }
}
