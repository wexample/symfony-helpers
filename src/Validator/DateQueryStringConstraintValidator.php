<?php

namespace Wexample\SymfonyHelpers\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Wexample\SymfonyHelpers\Helper\DateHelper;

class DateQueryStringConstraintValidator extends ConstraintValidator
{
    public function validate(
        $value,
        Constraint $constraint
    ): void {
        if (!$constraint instanceof DateQueryStringConstraint) {
            throw new UnexpectedTypeException($constraint, DateQueryStringConstraint::class);
        }

        if (!DateHelper::buildFromQueryStringDate($value)) {
            $this->context->buildViolation('Invalid date format.')
                ->atPath('date')
                ->addViolation();
        }
    }
}
