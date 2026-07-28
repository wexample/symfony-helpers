<?php

namespace Wexample\SymfonyHelpers\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wexample\Helpers\Helper\ClassHelper;

abstract class AbstractWexampleSymfonyExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $entityDir = __DIR__ . '/../Entity';

        if (!is_dir($entityDir)) {
            return;
        }

        $alias = str_replace('Extension', '', ClassHelper::getShortName(static::class));
        $entityNamespace = str_replace(
            '\\DependencyInjection',
            '\\Entity',
            substr(static::class, 0, strrpos(static::class, '\\'))
        );

        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    $alias => [
                        'type'      => 'attribute',
                        'dir'       => $entityDir,
                        'prefix'    => $entityNamespace,
                        'alias'     => $alias,
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);
    }

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
