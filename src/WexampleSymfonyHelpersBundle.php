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

        // Before the removing passes: those also inline the private services
        // used in one place, which then leave the list of definitions while
        // still living as long as the service holding them.
        $container->addCompilerPass(
            new WorkerSafeServicesCompilerPass(),
            PassConfig::TYPE_BEFORE_REMOVING
        );
    }
}
