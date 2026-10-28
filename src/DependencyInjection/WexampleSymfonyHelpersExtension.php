<?php

namespace Wexample\SymfonyHelpers\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\SymfonyHelpers\Normalizer\AbstractEntityNormalizer;
use Wexample\SymfonyHelpers\Normalizer\EntityNormalizerRegistry;

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

        // Every entity normalizer, wherever it is declared, for the registry
        // that says which one an entity goes to.
        $container->registerForAutoconfiguration(AbstractEntityNormalizer::class)
            ->addTag(EntityNormalizerRegistry::TAG);
    }
}
