<?php

namespace spec\Clearhaus\Api;

use Clearhaus\Api\Authorizations;
use Clearhaus\Client;
use Clearhaus\Exception\MissingArgumentException;
use Http\Client\Common\HttpMethodsClient;
use Prophecy\Argument;

class AuthorizationsSpec extends AbstractSpec
{
    function let(Client $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Authorizations::class);
    }

    function it_should_create_authorization(Client $client, HttpMethodsClient $httpClient)
    {
        $params = [
            'amount' => 2500,
            'currency' => 'EUR',
            'ip' => '1.1.1.1',
            'card' => [],
        ];

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->post(Argument::type('string'), Argument::type('array'), http_build_query($params))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->authorize($params)->shouldReturn($responseBodyAsArray);
    }

    function it_should_not_create_authorization_without_amount()
    {
        $params = [
            'currency' => 'EUR',
            'ip' => '1.1.1.1',
            'card' => [],
        ];

        $this->shouldThrow(MissingArgumentException::class)->duringAuthorize($params);
    }

    function it_should_not_create_authorization_without_currency()
    {
        $params = [
            'amount' => 2500,
            'ip' => '1.1.1.1',
            'card' => [],
        ];

        $this->shouldThrow(MissingArgumentException::class)->duringAuthorize($params);
    }

    function it_should_return_authorization(Client $client, HttpMethodsClient $httpClient)
    {
        $authorizationId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $responseBodyAsArray = ['id' => $authorizationId];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getAuthorization($authorizationId)->shouldReturn($responseBodyAsArray);
    }
}
