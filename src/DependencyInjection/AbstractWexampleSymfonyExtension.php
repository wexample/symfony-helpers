<?php

namespace Wexample\SymfonyHelpers\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

abstract class AbstractWexampleSymfonyExtension extends Extension
{
    protected function loadConfig(
        $callingDir,
        ContainerBuilder $container,
        $fileName = 'services.yaml'
    ): YamlFileLoader {
        $loader = new YamlFileLoader($container, new FileLocator($callingDir.'/../Resources/config'));
        $loader->load($fileName);

        return $loader;
    }
}
