<?php
declare(strict_types=1);

namespace Clearhaus\HttpClient;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Http\Message\RequestFactory;

class Builder
{
    /**
     * The object that sends HTTP messages.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * A HTTP client with all our plugins.
     *
     * @var PluginClient
     */
    private $pluginClient;

    /**
     * @var MessageFactory
     */
    private $requestFactory;

    /**
     * True if we should create a new Plugin client at next request.
     *
     * @var bool
     */
    private $httpClientModified = true;

    /**
     * @var Plugin[]
     */
    private $plugins = [];

    public function __construct(
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null
    ) {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    public function build() : HttpMethodsClient
    {
        if ($this->httpClientModified) {
            $this->httpClientModified = false;

            $this->pluginClient = new HttpMethodsClient(
                new PluginClient($this->httpClient, $this->plugins),
                $this->requestFactory
            );
        }

        return $this->pluginClient;
    }

    public function addPlugin(Plugin $plugin): void
    {
        $this->plugins[] = $plugin;
        $this->httpClientModified = true;
    }

    public function removePlugin(string $fqcn): void
    {
        foreach ($this->plugins as $index => $plugin) {
            if ($plugin instanceof $fqcn) {
                unset($this->plugins[$index]);
                $this->httpClientModified = true;
            }
        }
    }
}
