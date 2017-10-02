<?php

namespace spec\Clearhaus\Api;

use Clearhaus\Api\Voids;
use Clearhaus\Client;
use Http\Client\Common\HttpMethodsClient;
use Prophecy\Argument;

class VoidsSpec extends AbstractSpec
{
    function let(Client $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Voids::class);
    }

    function it_should_release_reserved_money(Client $client, HttpMethodsClient $httpClient)
    {
        $authorizationId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->post(Argument::type('string'), Argument::type('array'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->void($authorizationId)->shouldReturn($responseBodyAsArray);
    }

    function it_should_return_void(Client $client, HttpMethodsClient $httpClient)
    {
        $voidId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $responseBodyAsArray = ['id' => $voidId];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getVoid($voidId)->shouldReturn($responseBodyAsArray);
    }
}
