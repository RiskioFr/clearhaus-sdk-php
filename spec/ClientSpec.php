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
        $params = [
            'amount' => 2500,
            'currency' => 'EUR',
            'ip' => '1.1.1.1',
            'card' => [],
        ];

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client
            ->post(Argument::type('string'), Argument::type('array'), $params)
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

    function it_should_create_authorization_from_card_id(Builder $builder, HttpMethodsClient $client)
    {
        $cardId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = [
            'amount' => 2500,
            'currency' => 'EUR',
            'ip' => '1.1.1.1',
        ];

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client
            ->post(Argument::type('string'), Argument::type('array'), $params)
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->authorizeFromCardId($cardId, $params)->shouldReturn($responseBodyAsArray);
    }

    function it_should_not_create_authorization_from_card_id_without_amount()
    {
        $cardId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = [
            'currency' => 'EUR',
            'ip' => '1.1.1.1',
        ];

        $this->shouldThrow(MissingArgumentException::class)->duringAuthorizeFromCardId($cardId, $params);
    }

    function it_should_not_create_authorization_from_card_id_without_currency()
    {
        $cardId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = [
            'amount' => 2500,
            'ip' => '1.1.1.1',
        ];

        $this->shouldThrow(MissingArgumentException::class)->duringAuthorizeFromCardId($cardId, $params);
    }

    function it_should_return_authorization(Builder $builder, HttpMethodsClient $client)
    {
        $authorizationId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = ['id' => $authorizationId];

        $client
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getAuthorization($authorizationId)->shouldReturn($responseBodyAsArray);
    }

    function it_should_not_create_authorization_without_card()
    {
        $params = [
            'amount' => 2500,
            'currency' => 'EUR',
            'ip' => '1.1.1.1',
        ];

        $this->shouldThrow(MissingArgumentException::class)->duringAuthorize($params);
    }

    function it_should_capture_transaction(Builder $builder, HttpMethodsClient $client)
    {
        $authorizationId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = ['amount' => 2500];

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client
            ->post(Argument::type('string'), Argument::type('array'), $params)
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->capture($authorizationId, $params)->shouldReturn($responseBodyAsArray);
    }

    function it_should_return_capture(Builder $builder, HttpMethodsClient $client)
    {
        $captureId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = ['id' => $captureId];

        $client
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getCapture($captureId)->shouldReturn($responseBodyAsArray);
    }

    function it_should_make_a_refund(Builder $builder, HttpMethodsClient $client)
    {
        $authorizationId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = ['amount' => 500];

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client
            ->post(Argument::type('string'), Argument::type('array'), $params)
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->refund($authorizationId, $params)->shouldReturn($responseBodyAsArray);
    }

    function it_should_return_refund(Builder $builder, HttpMethodsClient $client)
    {
        $refundId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = ['id' => $refundId];

        $client
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getRefund($refundId)->shouldReturn($responseBodyAsArray);
    }

    function it_should_release_reserved_money(Builder $builder, HttpMethodsClient $client)
    {
        $authorizationId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client
            ->post(Argument::type('string'), Argument::type('array'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->void($authorizationId)->shouldReturn($responseBodyAsArray);
    }

    function it_should_return_void(Builder $builder, HttpMethodsClient $client)
    {
        $voidId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = ['id' => $voidId];

        $client
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getVoid($voidId)->shouldReturn($responseBodyAsArray);
    }

    function it_should_payout_money(Builder $builder, HttpMethodsClient $client)
    {
        $cardId = '84412a34-fa29-4369-a098-0165a80e8fda';
        $params = [
            'amount' => 2500,
            'currency' => 'EUR',
        ];

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = [
            'id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client
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

    function it_should_return_credit(Builder $builder, HttpMethodsClient $client)
    {
        $creditId = '84412a34-fa29-4369-a098-0165a80e8fda';

        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = ['id' => $creditId];

        $client
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getCredit($creditId)->shouldReturn($responseBodyAsArray);
    }

    function it_should_return_account(Builder $builder, HttpMethodsClient $client)
    {
        $builder->addPlugin(Argument::type(Plugin::class))->willReturn(null);
        $builder->build()->willReturn($client);

        $responseBodyAsArray = [
            'merchant_id' => '84412a34-fa29-4369-a098-0165a80e8fda',
        ];

        $client
            ->get(Argument::type('string'), Argument::type('array'))
            ->willReturn($this->createHttpResponse($responseBodyAsArray));

        $this->getAccount()->shouldReturn($responseBodyAsArray);
    }

    private function createHttpResponse(array $body) : Response
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
