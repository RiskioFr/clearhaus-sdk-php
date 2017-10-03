<?php

namespace spec\Clearhaus\Api;

use Clearhaus\Api\Credits;
use Clearhaus\Client;
use Clearhaus\Exception\MissingArgumentException;
use Http\Client\Common\HttpMethodsClient;
use Prophecy\Argument;

class CreditsSpec extends AbstractSpec
{
    function let(Client $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Credits::class);
    }

    function it_should_payout_money(Client $client, HttpMethodsClient $httpClient)
    {
        $cardId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = [
            'amount' => 2500,
            'currency' => 'EUR',
        ];

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->post(Argument::type('string'), Argument::type('array'), $params)
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->credit($cardId, $params)->shouldReturn($responseBodyAsArray);
    }

    function it_should_not_payout_money_without_amount()
    {
        $cardId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = ['currency' => 'EUR'];

        $this->shouldThrow(MissingArgumentException::class)->duringCredit($cardId, $params);
    }

    function it_should_not_payout_money_without_currency()
    {
        $cardId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = ['amount' => 2500];

        $this->shouldThrow(MissingArgumentException::class)->duringCredit($cardId, $params);
    }

    function it_should_return_credit(Client $client, HttpMethodsClient $httpClient)
    {
        $creditId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $responseBodyAsArray = ['id' => $creditId];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getCredit($creditId)->shouldReturn($responseBodyAsArray);
    }
}
