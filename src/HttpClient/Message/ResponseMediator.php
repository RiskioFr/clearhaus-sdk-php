<?php
declare(strict_types=1);

namespace Clearhaus\HttpClient\Message;

use Clearhaus\Client;
use Psr\Http\Message\ResponseInterface;

class ResponseMediator
{
    public static function getContent(ResponseInterface $response) : array
    {
        $body = $response->getBody()->__toString();
        $contentType = $response->getHeaderLine('Content-Type');

        if (\strpos($contentType, Client::CONTENT_TYPE) !== 0) {
            return [];
        }

        $content = \json_decode($body, true);
        if (JSON_ERROR_NONE !== \json_last_error()) {
            return [];
        }

        return $content;
    }

    public static function getHeader(ResponseInterface $response, $name) : string
    {
        $headers = $response->getHeader($name);

        return (string) \array_shift($headers);
    }
}
