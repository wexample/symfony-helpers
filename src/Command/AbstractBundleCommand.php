<?php

namespace Wexample\SymfonyHelpers\Command;

use Wexample\SymfonyHelpers\Service\BundleService;

abstract class AbstractBundleCommand extends AbstractCommand
{
    public function __construct(
        protected BundleService $bundleService,
        string $name = null,
    ) {
        parent::__construct(
            $name
        );
    }
}
