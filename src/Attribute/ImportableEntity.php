<?php

namespace Wexample\SymfonyHelpers\Attribute;

use Attribute;

/**
 * Marks an entity that can be fed by the import pipeline.
 *
 * Filestate scaffolds its import DTO and the matching validation DTO.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ImportableEntity
{
}
