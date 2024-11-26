<?php

namespace Wexample\SymfonyHelpers\Migration\Traits;

use Wexample\SymfonyHelpers\Helper\ClassHelper;

trait WithDataMigrationTrait
{
    protected function buildMigrationDataFilPath(string $extension): string
    {
        return 'migrations/'.ClassHelper::getShortName(static::class).'.'.$extension;
    }

    protected function loadMigrationData(): array
    {
        return json_decode(file_get_contents($this->buildMigrationDataFilPath('json')), true);
    }

    protected function addMigrationSqlFile(): void
    {
        $sqlFilePath = $this->buildMigrationDataFilPath('sql');

        if (file_exists($sqlFilePath)) {
            $sqlStatements = file($sqlFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($sqlStatements as $sql) {
                $this->addSql($sql);
            }
        }
    }
}