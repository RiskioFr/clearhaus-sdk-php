<?php

namespace Clearhaus\HttpClient;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Http\Message\RequestFactory;

final class Builder
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

    /**
     * Http headers.
     *
     * @var array
     */
    private $headers = [];

    public function __construct(
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null
    ) {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    public function getHttpClient() : HttpMethodsClient
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

    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;
        $this->httpClientModified = true;
    }

    public function removePlugin(string $fqcn)
    {
        foreach ($this->plugins as $idx => $plugin) {
            if ($plugin instanceof $fqcn) {
                unset($this->plugins[$idx]);
                $this->httpClientModified = true;
            }
        }
    }

    public function clearHeaders()
    {
        $this->headers = [];

        $this->removePlugin(Plugin\HeaderAppendPlugin::class);
        $this->addPlugin(new Plugin\HeaderAppendPlugin($this->headers));
    }

    public function addHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        $this->removePlugin(Plugin\HeaderAppendPlugin::class);
        $this->addPlugin(new Plugin\HeaderAppendPlugin($this->headers));
    }

    public function addHeaderValue(string $header, string $headerValue)
    {
        if (!isset($this->headers[$header])) {
            $this->headers[$header] = $headerValue;
        } else {
            $this->headers[$header] = array_merge((array)$this->headers[$header], array($headerValue));
        }

        $this->removePlugin(Plugin\HeaderAppendPlugin::class);
        $this->addPlugin(new Plugin\HeaderAppendPlugin($this->headers));
    }
}
