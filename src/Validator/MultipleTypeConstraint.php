<?php

namespace Wexample\SymfonyHelpers\Validator;

use Symfony\Component\Validator\Constraint;

class MultipleTypeConstraint extends Constraint
{
    public array $types = [];
    public string $message = 'The value {{ value }} is not a valid Type.';

    public function __construct(
        array $types,
        mixed $options = null,
        array $groups = null,
        mixed $payload = null
    ) {
        $options['types'] = $types;

        parent::__construct($options, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return \get_class($this).'Validator';
    }

    public function getDefaultOption(): string
    {
        return 'types';
    }
}
