<?php

namespace spec\Clearhaus\HttpClient;

use Clearhaus\HttpClient\Builder;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BuilderSpec extends ObjectBehavior
{
    function let(HttpClient $httpClient, RequestFactory $requestFactory)
    {
        $this->beConstructedWith($httpClient, $requestFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Builder::class);
    }

    function is_shoud_return_http_client()
    {
        $this->build()->shouldReturnAnInstanceOf(HttpMethodsClient::class);
    }
}
