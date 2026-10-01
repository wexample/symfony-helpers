# Sapiens — PostgreSQL helpers for Doctrine

Opened: 2026-10-01
Updated: 2026-10-01
Author: agent:sapiens

## Context

Asked by the Sapiens app (`HOME_HABILIS/local/sapiens`). Three PostgreSQL needs that Sapiens solved by hand, and that any application on Postgres meets. They belong next to `Doctrine/Type/VectorType.php`.

## 1. `UNACCENT` in DQL

- A DQL string function `UNACCENT(expr)` that emits `unaccent(...)`, so that a search for "elodie" finds "Élodie". Sapiens has it in `src/Doctrine/Function/UnaccentFunction.php`, registered in `doctrine.yaml` under `dql.string_functions`.
- The `unaccent` extension must exist: provide the migration line (`CREATE EXTENSION IF NOT EXISTS unaccent`) or a helper to call from a migration, and document it. Without the extension, the error appears only on the first search.
- Register it from the bundle (prepend the Doctrine config) when the platform is PostgreSQL, so that the application does not have to.
- Document the usual pairing: `LOWER(UNACCENT(field)) LIKE LOWER(UNACCENT(:term))`, with the term's `%` and `_` escaped.

## 2. The latest row per group (`DISTINCT ON`)

- "The latest measurement of each patient" is a classic query. DQL cannot express it, and the alternatives (a subquery on `MAX`, a window function) are slow or verbose. Sapiens runs a native `SELECT DISTINCT ON (patient_id) id … ORDER BY patient_id, date_measured DESC`, then loads the entities by id (`MeasurementRepository::findLatestByPatients`).
- A repository helper: given an entity, the grouping column, the ordering column and direction, and the group ids, it returns the latest entity of each group, indexed by group id. It is written once and tested once.

## 3. `CHECK` constraints on a JSON roles column

- Doctrine serialises `roles` as `json`. A `CHECK` constraint that tests membership of a value in it must use `jsonb_exists(roles::jsonb, 'ROLE_X')`, not the `?` operator, which PDO takes for a placeholder. Sapiens hit this in its first migration (`user_it_role_exclusive`).
- At minimum, document it. Better, a migration helper: `addJsonContainsCheck(table, column, name, expression)`, or a function producing the SQL expression for "contains X" and "contains X and none of Y, Z".

## Tests

- `UNACCENT` finds an accented value from an unaccented term, and the reverse.
- The latest-per-group helper returns one row per group, the latest one, and an empty array for no group.
- A migration using the `CHECK` helper runs through PDO without a placeholder error.
