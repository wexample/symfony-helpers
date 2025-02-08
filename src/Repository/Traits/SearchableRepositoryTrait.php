<?php

namespace Wexample\SymfonyHelpers\Repository\Traits;

use Doctrine\ORM\QueryBuilder;
use Wexample\Helpers\Helper\TextHelper;

trait SearchableRepositoryTrait
{
    public const QUERY_SEARCH_METHOD_EQUAL = 'eq';

    public const QUERY_SEARCH_METHOD_LIKE = 'like';

    public const QUERY_SEARCH_PARAMETER_NAME_DEFAULT = 'stringEqual';

    /**
     * Process builder when a search action is provided.
     *
     * @return mixed
     */
    abstract public function querySearch(
        QueryBuilder $builder,
        string $string
    );

    public function querySearchStringOrArray(
        string $field,
        array|string $value,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $builder = $this->createOrGetQueryBuilder($builder);

        if (is_array($value)) {
            return $this->queryIsInArray(
                $field,
                $value,
                $builder
            );
        }

        $builder
            ->andWhere(
                $this->queryField($field).' = :'.$field
            )
            ->setParameter(
                $field,
                $value
            );

        return $builder;
    }

    public function querySearchLike(
        QueryBuilder $builder,
        array|string $fields,
        $value,
        string $parameterName = 'stringLike'
    ) {
        $this->querySearchEqual(
            $builder,
            $fields,
            '%'.$value.'%',
            $parameterName,
            self::QUERY_SEARCH_METHOD_LIKE
        );
    }

    public function querySearchEqual(
        QueryBuilder $builder,
        array|string $fields,
        mixed $value,
        string $parameterName = self::QUERY_SEARCH_PARAMETER_NAME_DEFAULT,
        string $method = self::QUERY_SEARCH_METHOD_EQUAL,
        bool $wrapFieldsNames = true
    ) {
        $fields = is_array($fields) ? $fields : [$fields];
        $orX = $builder->expr()->orX();

        foreach ($fields as $field) {
            $orX->add(
                $builder
                    ->expr()
                    ->$method(
                        $wrapFieldsNames ? $this->queryField($field) : $field,
                        ':'.$parameterName
                    )
            );
        }

        $builder->orWhere($orX);

        if (!empty($fields)) {
            $builder->setParameter($parameterName, $value);
        }
    }

    public function querySelectEntity(QueryBuilder $builder)
    {
        $builder
            ->addSelect(
                $this->getEntityQueryAlias()
            )
            ->from(
                $this->_entityName,
                $this->getEntityQueryAlias()
            );

        return $this;
    }

    public function querySearchNumberAbsolute(
        QueryBuilder $builder,
        $fields,
        $value
    ) {
        $fields = is_array($fields) ? $fields : [$fields];

        $fieldsConverted = [];
        foreach ($fields as $field) {
            $fieldsConverted[] = 'ABS('.$this->queryField($field).')';
        }

        $this->querySearchNumber(
            $builder,
            $fieldsConverted,
            abs($value),
            false
        );
    }

    public function querySearchNumber(
        QueryBuilder $builder,
        array|string $fields,
        float|int $value,
        bool $wrapFieldsNames = true
    ) {
        $this->querySearchEqual(
            $builder,
            $fields,
            $value,
            self::QUERY_SEARCH_PARAMETER_NAME_DEFAULT,
            self::QUERY_SEARCH_METHOD_EQUAL,
            $wrapFieldsNames
        );
    }

    public function querySearchEqualNonZeroIntData(
        QueryBuilder $builder,
        $fields,
        string $value
    ) {
        // Transform 1,50 to 150.
        $number = TextHelper::getIntDataFromString($value);
        if ($number) {
            $this->querySearchEqual(
                $builder,
                $fields,
                $number
            );
        }
    }
}
