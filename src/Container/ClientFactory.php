<?php
declare(strict_types=1);

namespace Clearhaus\Container;

use Clearhaus\Client;
use Clearhaus\Exception\RuntimeException;
use Clearhaus\HttpClient\Builder;
use Psr\Container\ContainerInterface;

final class ClientFactory
{
    private $configKey;

    public function __construct(string $configKey = 'clearhaus_sdk')
    {
        $this->configKey = $configKey;
    }

    public function __invoke(ContainerInterface $container) : Client
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $moduleConfig = $config[$this->configKey] ?: [];

        if (empty($moduleConfig['api_key'])) {
            throw new RuntimeException('An API key must be specified.');
        }

        $builder = $this->createBuilder($moduleConfig, $container);
        $mode = $moduleConfig['mode'] ?: Client::MODE_TEST;

        $client = new Client($moduleConfig['api_key'], $builder, $mode);

        if (isset($moduleConfig['use_signature']) && $moduleConfig['use_signature']) {
            $client->enableSignature();
        }

        return $client;
    }

    private function createBuilder(array $config, ContainerInterface $container) : Builder
    {
        if (isset($config['builder'])) {
            $builder = $container->get($config['builder']);
        } else {
            $builder = new Builder();
        }

        if (isset($config['plugins']) && is_array($config['plugins'])) {
            foreach ($config['plugins'] as $fcqn) {
                $builder->addPlugin($container->get($fcqn));
            }
        }

        return $builder;
    }
}
