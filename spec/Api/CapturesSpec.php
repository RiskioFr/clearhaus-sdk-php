<?php

namespace spec\Clearhaus\Api;

use Clearhaus\Api\Captures;
use Clearhaus\Client;
use Http\Client\Common\HttpMethodsClient;
use Prophecy\Argument;

class CapturesSpec extends AbstractSpec
{
    function let(Client $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Captures::class);
    }

    function it_should_capture_transaction(Client $client, HttpMethodsClient $httpClient)
    {
        $authorizationId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = ['amount' => 2500];

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->post(Argument::type('string'), Argument::type('array'), $params)
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->capture($authorizationId, $params)->shouldReturn($responseBodyAsArray);
    }

    function it_should_return_capture(Client $client, HttpMethodsClient $httpClient)
    {
        $captureId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $responseBodyAsArray = ['id' => $captureId];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getCapture($captureId)->shouldReturn($responseBodyAsArray);
    }
}
