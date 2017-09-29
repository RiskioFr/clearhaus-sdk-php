<?php

namespace Clearhaus\HttpClient\Message;

use Psr\Http\Message\ResponseInterface;

class ResponseMediator
{
    public static function getContent(ResponseInterface $response) : array
    {
        $body = $response->getBody()->__toString();
        $contentType = $response->getHeaderLine('Content-Type');

        if (strpos($contentType, 'application/vnd.clearhaus-gateway.hal+json') !== 0) {
            return [];
        }

        $content = json_decode($body, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return [];
        }

        return $content;
    }

    public static function getHeader(ResponseInterface $response, $name) : string
    {
        $headers = $response->getHeader($name);

        return (string) array_shift($headers);
    }
}
