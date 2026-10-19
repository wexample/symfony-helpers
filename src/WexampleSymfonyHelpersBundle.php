<?php

namespace Wexample\SymfonyHelpers;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\SymfonyHelpers\Class\AbstractBundle;
use Wexample\SymfonyHelpers\DependencyInjection\Compiler\WorkerSafeServicesCompilerPass;

class WexampleSymfonyHelpersBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Once the unused definitions are gone: only the services the
        // container keeps can carry anything from one request to the next.
        $container->addCompilerPass(
            new WorkerSafeServicesCompilerPass(),
            PassConfig::TYPE_AFTER_REMOVING
        );
    }
}
