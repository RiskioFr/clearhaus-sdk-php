<?php
declare(strict_types=1);

namespace Clearhaus;

use Clearhaus\Api;
use Clearhaus\Exception\BadMethodCallException;
use Clearhaus\Exception\InvalidArgumentException;
use Clearhaus\HttpClient\Builder;
use Clearhaus\HttpClient\Plugin\ErrorPlugin;
use Clearhaus\HttpClient\Plugin\SignaturePlugin;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\Authentication\BasicAuth;
use phpseclib\Crypt\RSA;

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

    private $apiKey;

    private $builder;

    public function __construct(string $apiKey, Builder $builder = null, string $mode = self::MODE_TEST)
    {
        $this->apiKey = $apiKey;
        $this->builder = $builder ?: new Builder();

        $uri = $mode === self::MODE_TEST ? self::ENDPOINT_TEST : self::ENDPOINT_LIVE;

        $this->builder->addPlugin(new ErrorPlugin());
        $this->builder->addPlugin(new Plugin\AddHostPlugin(
            Psr17FactoryDiscovery::findUrlFactory()->createUri($uri)
        ));
        $this->builder->addPlugin(new AuthenticationPlugin(
            new BasicAuth($this->apiKey, '')
        ));
    }

    public function enableSignature()
    {
        $encrypter = new RSA();
        $this->builder->addPlugin(new SignaturePlugin($encrypter, $this->apiKey));
    }

    public function getHttpClient() : HttpMethodsClient
    {
        return $this->builder->build();
    }

    public function __call($name, $args) : Api\AbstractApi
    {
        try {
            return $this->api($name);
        } catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(\sprintf('Undefined method called: "%s"', $name));
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
                    \sprintf('Undefined api instance called: "%s"', $name)
                );
        }
    }
}
