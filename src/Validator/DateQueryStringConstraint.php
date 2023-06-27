<?php

namespace Wexample\SymfonyHelpers\Validator;

use Symfony\Component\Validator\Constraint;
use function get_class;

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