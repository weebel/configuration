<?php

namespace Weebel\Configuration;

use Weebel\Contracts\Container;

class ConfigurationProvider
{
    public function __construct(protected Container $container)
    {
    }

    public function __invoke(): void
    {
        $this->container->alias(\Weebel\Contracts\Configuration::class, Configuration::class);
    }

}