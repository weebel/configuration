<?php

namespace Weebel\Configuration;

use Weebel\Contracts\Bootable;
use Weebel\Contracts\Container;

class ConfigurationProvider implements Bootable
{
    public function __construct(protected Container $container)
    {
    }

    public function boot(): void
    {
        $this->container->alias(\Weebel\Contracts\Configuration::class, Configuration::class);
    }
}