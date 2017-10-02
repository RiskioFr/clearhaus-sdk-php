<?php

namespace spec\Clearhaus;

use Clearhaus\Client;
use Clearhaus\HttpClient\Builder;
use Clearhaus\HttpClient\Plugin\Authentication;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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
}
