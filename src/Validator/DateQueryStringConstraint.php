<?php

namespace Wexample\SymfonyHelpers\Validator;

use function get_class;

use Symfony\Component\Validator\Constraint;

class DateQueryStringConstraint extends Constraint
{
    public array $types = [];
    public string $message = 'The value {{ value }} is not a valid Type.';

    public function validatedBy(): string
    {
        return get_class($this).'Validator';
    }

    public function getDefaultOption(): string
    {
        return 'types';
    }
}
