<?php

namespace Wexample\SymfonyHelpers\Traits;

trait WithTableNameTrait
{
    private string $tableName;

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }
}
