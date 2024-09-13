<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

trait HasDisplayName
{
    abstract public function getDisplayName(): string;
}
