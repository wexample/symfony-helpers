<?php

namespace Wexample\SymfonyHelpers\Migration\Traits;

trait EntityInsertionMigrationTrait
{
    protected function addSqlToUpdateAllSequences(): void
    {
        $sql = "SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
              AND table_type = 'BASE TABLE';";

        $tables = $this->connection->fetchAllAssociative($sql);

        foreach ($tables as $table) {
            $tableName = $table['table_name'];
            $sequenceName = $tableName . '_id_seq';

            $idCheckSql = "SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = '$tableName' 
                  AND column_name = 'id';";
            $hasIdColumn = $this->connection->fetchOne($idCheckSql);

            if ($hasIdColumn) {
                $this->addSql("SELECT setval('$sequenceName', (SELECT COALESCE(MAX(id), 1) FROM \"$tableName\"))");
            }
        }
    }
}