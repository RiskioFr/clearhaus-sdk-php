<?php

namespace spec\Clearhaus\Api;

use Clearhaus\Api\Refunds;
use Clearhaus\Client;
use Http\Client\Common\HttpMethodsClient;
use Prophecy\Argument;

class RefundsSpec extends AbstractSpec
{
    function let(Client $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Refunds::class);
    }

    function it_should_make_a_refund(Client $client, HttpMethodsClient $httpClient)
    {
        $authorizationId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = ['amount' => 500];

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->post(Argument::type('string'), Argument::type('array'), $params)
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->refund($authorizationId, $params)->shouldReturn($responseBodyAsArray);
    }

    function it_should_return_refund(Client $client, HttpMethodsClient $httpClient)
    {
        $refundId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $responseBodyAsArray = ['id' => $refundId];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getRefund($refundId)->shouldReturn($responseBodyAsArray);
    }
}
