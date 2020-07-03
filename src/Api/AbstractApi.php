<?php
declare(strict_types=1);

namespace Clearhaus\Api;

use Clearhaus\Client;
use Clearhaus\HttpClient\Message\ResponseMediator;

abstract class AbstractApi
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function post(string $path, array $params, array $headers = []) : array
    {
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $response = $this->client->getHttpClient()->post($path, $headers, \http_build_query($params));

        return ResponseMediator::getContent($response);
    }

    public function get(string $path, array $headers = []) : array
    {
        $response = $this->client->getHttpClient()->get($path, $headers);

        return ResponseMediator::getContent($response);
    }
}
