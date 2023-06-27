<?php

namespace Wexample\SymfonyHelpers\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class WexampleSymfonyHelpersExtension extends AbstractWexampleSymfonyExtension
{
    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $this->loadConfig(
            __DIR__,
            $container
        );
    }
}
