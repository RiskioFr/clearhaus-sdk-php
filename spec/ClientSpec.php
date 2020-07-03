<?php

namespace spec\Clearhaus;

use Clearhaus\Client;
use Clearhaus\HttpClient\Builder;
use Clearhaus\HttpClient\Plugin\SignaturePlugin;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClientSpec extends ObjectBehavior
{
    private $apiKey = '123456789';

    function let(Builder $builder)
    {
        $this->beConstructedWith($this->apiKey, $builder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_should_enable_signature(Builder $builder)
    {
        $builder->addPlugin(Argument::type(Plugin::class))->shouldBeCalled();

        $builder->addPlugin(Argument::type(SignaturePlugin::class))->shouldBeCalled();

        $this->enableSignature();
    }

    function it_should_return_http_client(Builder $builder, HttpMethodsClient $httpMethodsClient)
    {
        $builder->addPlugin(Argument::type(Plugin::class))->shouldBeCalled();

        $builder->build()->willReturn($httpMethodsClient);

        $this->getHttpClient()->shouldReturn($httpMethodsClient);
    }
}
