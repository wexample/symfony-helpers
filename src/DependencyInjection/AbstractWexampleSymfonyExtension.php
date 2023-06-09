<?php

namespace Wexample\SymfonyHelpers\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

abstract class AbstractWexampleSymfonyExtension extends Extension
{
    protected function loadServices(
        $callingDir,
        ContainerBuilder $container
    ) {
        $loader = new YamlFileLoader($container, new FileLocator($callingDir.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
