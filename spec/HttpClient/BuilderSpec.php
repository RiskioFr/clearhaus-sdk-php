<?php

namespace spec\Clearhaus\HttpClient;

use Clearhaus\HttpClient\Builder;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use PhpSpec\ObjectBehavior;

class BuilderSpec extends ObjectBehavior
{
    function let(HttpClient $httpClient)
    {
        $requestFactory = MessageFactoryDiscovery::find();

        $this->beConstructedWith($httpClient, $requestFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Builder::class);
    }

    function it_shoud_return_http_client()
    {
        $this->build()->shouldReturnAnInstanceOf(HttpMethodsClient::class);
    }
}
