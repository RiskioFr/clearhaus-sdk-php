<?php

namespace spec\Clearhaus;

use Clearhaus\Client;
use Clearhaus\Exception\MissingArgumentException;
use Clearhaus\HttpClient\Builder;
use Clearhaus\HttpClient\Plugin\Authentication;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class ClientSpec extends ObjectBehavior
{
    function let(Builder $builder)
    {
        $this->beConstructedWith($builder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_should_add_authentication_plugin(Builder $builder)
    {
        $apiKey = '123456789';

        $builder->addPlugin(Argument::type(Authentication::class));

        $this->setApiKey($apiKey)->shouldReturn(null);
    }

    function it_should_create_authorization(Builder $builder, HttpMethodsClient $client)
    {
        $authorizeParams = [
            'amount' => 2500,
            'currency' => 'EUR',
            'ip' => '1.1.1.1',
            'card' => [],
        ];

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
            'status' => [
                'code' => 20000,
            ],
            'processed_at' => '2014-07-09T09:53:41+00:00',
            '_links' => [
                'captures' => [
                    'href' => '/authorizations/84412a34-fa29-4369-a098-0165a80e8fda/captures',
                ],
            ],
        ];

        $httpResponse = $this->createHttpResponse($responseBodyAsArray);

        $client
            ->post(Argument::type('string'), Argument::type('array'), $authorizeParams)
            ->willReturn($httpResponse);

        $this->authorize($authorizeParams)->shouldReturn($responseBodyAsArray);
    }

    function it_should_not_create_authorization_without_amount()
    {
        $authorizeParams = [
            'currency' => 'EUR',
            'ip' => '1.1.1.1',
            'card' => [],
        ];

        $this->shouldThrow(MissingArgumentException::class)->duringAuthorize($authorizeParams);
    }

    function it_should_not_create_authorization_without_currency()
    {
        $authorizeParams = [
            'amount' => 2500,
            'ip' => '1.1.1.1',
            'card' => [],
        ];

        $this->shouldThrow(MissingArgumentException::class)->duringAuthorize($authorizeParams);
    }

    function it_should_not_create_authorization_without_ip_address()
    {
        $authorizeParams = [
            'amount' => 2500,
            'currency' => 'EUR',
            'card' => [],
        ];

        $this->shouldThrow(MissingArgumentException::class)->duringAuthorize($authorizeParams);
    }

    function it_should_not_create_authorization_without_card()
    {
        $authorizeParams = [
            'amount' => 2500,
            'currency' => 'EUR',
            'ip' => '1.1.1.1',
        ];

        $this->shouldThrow(MissingArgumentException::class)->duringAuthorize($authorizeParams);
    }

    private function createHttpResponse(array $body) : Response
    {
        $stream = new Stream('php://memory', 'rw');
        $stream->write(json_encode($body, JSON_FORCE_OBJECT));
        $stream->rewind();

        $httpResponse = (new Response())
            ->withHeader('Content-Type', 'application/vnd.clearhaus-gateway.hal+json')
            ->withBody($stream);

        return $httpResponse;
    }
}
