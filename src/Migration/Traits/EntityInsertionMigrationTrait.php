<?php

namespace Wexample\SymfonyHelpers\Migration\Traits;

trait EntityInsertionMigrationTrait
{
    protected function addSqlToUpdateAllSequences(): void
    {
        $this->addSql("
        DO $$ 
        DECLARE 
            seq RECORD;
        BEGIN
            -- Boucle sur toutes les séquences de la base de données
            FOR seq IN 
                SELECT 
                    sequence_name, 
                    table_name, 
                    column_name 
                FROM information_schema.sequences s
                JOIN information_schema.columns c 
                ON s.sequence_name = (c.table_name || '_' || c.column_name || '_seq')
                WHERE sequence_schema = 'public'
            LOOP
                -- Met à jour chaque séquence en fonction de la valeur maximale de la colonne associée
                EXECUTE format(
                    'SELECT setval(pg_get_serial_sequence(''%I'', ''%I''), COALESCE(MAX(%I), 1)) FROM %I',
                    seq.table_name,
                    seq.column_name,
                    seq.column_name,
                    seq.table_name
                );
            END LOOP;
        END $$;
    ");
    }
}