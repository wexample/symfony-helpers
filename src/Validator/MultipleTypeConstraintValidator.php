<?php

namespace Wexample\SymfonyHelpers\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MultipleTypeConstraintValidator extends ConstraintValidator
{
    public function validate(
        $value,
        Constraint $constraint
    ): void {
        if (!$constraint instanceof MultipleTypeConstraint) {
            throw new UnexpectedTypeException($constraint, MultipleTypeConstraint::class);
        }

        // Check if the value is one of the allowed types
        foreach ($constraint->types as $type) {
            settype($value, $type);
            if (gettype($value) === $type) {
                return;
            }
        }

        // If not, it's an error
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
