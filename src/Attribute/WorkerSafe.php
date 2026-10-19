<?php

namespace Wexample\SymfonyHelpers\Attribute;

use Attribute;

/**
 * A service, or one of its properties, that is not readonly and yet keeps
 * nothing a request may leave to the next one a worker serves: a memo of what
 * the code or the configuration says, the same for every visitor. The reason
 * is required, for whoever reads it to be able to dispute it.
 *
 * @see \Wexample\SymfonyHelpers\DependencyInjection\Compiler\WorkerSafeServicesCompilerPass
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
final readonly class WorkerSafe
{
    public function __construct(
        public string $reason,
    ) {
    }
}
