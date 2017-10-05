<?php

namespace spec\Clearhaus\Container;

use Clearhaus\Client;
use Clearhaus\Container\ClientFactory;
use Clearhaus\HttpClient\Builder;
use PhpSpec\ObjectBehavior;
use Psr\Container\ContainerInterface;

class ClientFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ClientFactory::class);
    }

    function it_should_return_client(Builder $builder, ContainerInterface $container)
    {
        $config = [
            'clearhaus_sdk' => [
                'api_key' => '123456789',
                'builder' => Builder::class,
                'mode' => Client::MODE_TEST,
            ],
        ];

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);
        $container->get($config['clearhaus_sdk']['builder'])->willReturn($builder);

        $this->__invoke($container)->shouldReturnAnInstanceOf(Client::class);
    }
}
