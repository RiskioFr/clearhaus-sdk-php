<?php

namespace spec\Clearhaus\Api;

use Clearhaus\Client;
use PhpSpec\ObjectBehavior;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

abstract class AbstractSpec extends ObjectBehavior
{
    protected function createHttpResponse(array $body) : Response
    {
        $stream = new Stream('php://memory', 'rw');
        $stream->write(json_encode($body, JSON_FORCE_OBJECT));
        $stream->rewind();

        $httpResponse = (new Response())
            ->withHeader('Content-Type', Client::CONTENT_TYPE)
            ->withBody($stream);

        return $httpResponse;
    }
}
