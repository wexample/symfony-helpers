<?php

namespace Wexample\SymfonyHelpers\Migration\Traits;

use Wexample\SymfonyHelpers\Helper\ClassHelper;

trait WithDataMigrationTrait
{
    protected function loadMigrationData(): array
    {
        return json_decode(file_get_contents('migrations/'.ClassHelper::getShortName(static::class).'.json'), true);
    }
}