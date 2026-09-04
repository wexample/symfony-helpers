<?php

namespace Wexample\SymfonyHelpers\Attribute;

use Attribute;

/**
 * Marks an entity other entities are meant to point at.
 *
 * Filestate scaffolds a `LinkedTo{Entity}Trait` for each one, carrying the
 * ManyToOne relation and its accessors.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class LinkableEntity
{
}
