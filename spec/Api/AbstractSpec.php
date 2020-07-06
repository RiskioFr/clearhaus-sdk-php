<?php

namespace spec\Clearhaus\Api;

use Clearhaus\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use PhpSpec\ObjectBehavior;

abstract class AbstractSpec extends ObjectBehavior
{
    protected function createHttpResponse(array $body) : Response
    {
        $stream = Psr7\stream_for(\json_encode($body, JSON_FORCE_OBJECT));
        $stream->rewind();

        $httpResponse = (new Response())
            ->withHeader('Content-Type', Client::CONTENT_TYPE)
            ->withBody($stream);

        return $httpResponse;
    }
}
