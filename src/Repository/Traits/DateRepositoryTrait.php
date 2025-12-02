<?php

namespace Wexample\SymfonyHelpers\Repository\Traits;

use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Helper\DateHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

trait DateRepositoryTrait
{
    /**
     * Filter on a date field for a given month.
     * Allow array as date fields, in this case filter on the first non-null
     * field found.
     *
     * @param                   $fields
     * @param DateTimeInterface $dateMonth
     * @param QueryBuilder|null $builder
     * @param string $whereQuery
     * @return QueryBuilder
     */
    public function queryDateInMonth(
        $fields,
        DateTimeInterface $dateMonth,
        QueryBuilder $builder = null,
        string $whereQuery = ''
    ): QueryBuilder {
        return $this->queryDateRange(
            $fields,
            DateHelper::startOfMonth($dateMonth),
            DateHelper::endOfMonth($dateMonth),
            $builder,
            $whereQuery
        );
    }

    public function queryDateBefore(
        string $field,
        DateTimeInterface $date,
        QueryBuilder $builder = null
    ): QueryBuilder {
        return $this->queryDateBoundary(
            $field,
            $date,
            'Start',
            '<=',
            $builder
        );
    }

    public function queryDateBoundary(
        string $field,
        DateTimeInterface $date,
        string $parameterSuffix,
        string $operator,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder($builder);

        $fieldFull = $this->queryField($field);

        $builder
            ->andWhere(
                $fieldFull . ' IS NOT NULL AND ' . $fieldFull . ' ' . $operator . ' :' . $field . 'Date' . $parameterSuffix
            );

        $builder
            ->setParameter(
                $field . 'Date' . $parameterSuffix,
                $date->format('Y-m-d H:i:s')
            );

        return $builder;
    }

    public function queryDateAfter(
        string $field,
        DateTimeInterface $date,
        QueryBuilder $builder = null
    ): QueryBuilder {
        return $this->queryDateBoundary(
            $field,
            $date,
            'Start',
            '>=',
            $builder
        );
    }

    public function queryDateRange(
        $fields,
        DateTimeInterface $dateFirst,
        DateTimeInterface $dateLast,
        QueryBuilder $builder = null,
        string $whereQuery = ''
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder($builder);

        if (! $whereQuery) {
            if (is_string($fields)) {
                $fields = [$fields];
            }

            $whereQuery = [];
            $fieldFullPrevious = null;

            foreach ($fields as $field) {
                $fieldFull = $this->queryField($field);

                $query = '( ';

                if ($fieldFullPrevious) {
                    $query .= $fieldFullPrevious . ' IS NULL AND ';
                }

                $query .= $fieldFull . ' IS NOT NULL AND ' .
                    $fieldFull .
                    ' >= :' . $field . 'RangeStart AND ' .
                    $fieldFull .
                    ' <= :' . $field . 'RangeEnd )';

                $whereQuery[] = $query;

                $builder
                    ->setParameter(
                        $field . 'RangeStart',
                        $dateFirst->format('Y-m-d H:i:s')
                    )
                    ->setParameter(
                        $field . 'RangeEnd',
                        $dateLast->format('Y-m-d H:i:s')
                    );

                $fieldFullPrevious = $fieldFull;
            }

            $whereQuery = implode(' OR ', $whereQuery);
        }

        $builder
            ->andWhere($whereQuery);

        return $builder;
    }

    public function findOneByYear(
        DateTimeInterface $dateAccounting
    ): ?AbstractEntity {
        return $this->findOneBy([
            VariableHelper::YEAR => ((int) $dateAccounting->format(DateHelper::DATE_PATTERN_PART_YEAR_FULL)),
        ]);
    }

    public function queryDateInYear(
        $fields,
        DateTimeInterface $dateYear,
        bool $ordered = true,
        string $whereQuery = '',
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->queryDateRange(
            $fields,
            DateHelper::startOfYear($dateYear),
            DateHelper::endOfYear($dateYear),
            $builder,
            $whereQuery
        );

        if ($ordered) {
            $fields = is_array($fields) ? $fields : [$fields];

            foreach ($fields as $field) {
                $this->orderByDateField(
                    $field,
                    $builder
                );
            }
        }

        return $builder;
    }

    public function orderByDateField(
        string $field,
        QueryBuilder $builder = null,
        string $order = AbstractRepository::SORT_ASC
    ): void {
        $builder = $this->createOrGetQueryBuilder($builder);

        $builder->orderBy(
            $this->queryField($field),
            // Enforce sorting as it is the only interest on this shortcut method.
            $order
        );
    }

    public function queryLastByDateField(
        string $field,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $this->orderByDateField(
            field: $field,
            builder: $builder,
            order: AbstractRepository::SORT_DESC
        );

        $builder->setMaxResults(1);

        return $builder;
    }

    /**
     * By default on postgres, when a date field can be null, the null values is ordered **before** the most recent date.
     * Use this method to put the null dates **after** the older date.
     *
     * @param string $fieldName
     * @param QueryBuilder|null $builder
     * @return QueryBuilder
     */
    public function queryOrderByDateAndNull(
        string $fieldName,
        QueryBuilder $builder = null,
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder($builder);

        $builder->orderBy(
            'CASE WHEN ' . $this->queryField($fieldName) . ' IS NULL THEN 0 ELSE 1 END',
            order: self::SORT_DESC
        )->addOrderBy(
            $this->queryField($fieldName),
            order: self::SORT_DESC
        );

        return $builder;
    }
}
