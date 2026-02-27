<?php

namespace Wexample\SymfonyHelpers\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RectifiableEntity
{
    public function __construct(
        public readonly array $config = []
    ) {
    }
}
