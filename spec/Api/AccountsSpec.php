<?php

namespace spec\Clearhaus\Api;

use Clearhaus\Api\Accounts;
use Clearhaus\Client;
use Http\Client\Common\HttpMethodsClient;
use Prophecy\Argument;

class AccountsSpec extends AbstractSpec
{
    function let(Client $client)
    {
        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Accounts::class);
    }

    function it_should_return_account(Client $client, HttpMethodsClient $httpClient)
    {
        $responseBodyAsArray = [
            'merchant_id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client->getHttpClient()->willReturn($httpClient);

        $httpClient
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getAccount()->shouldReturn($responseBodyAsArray);
    }
}
