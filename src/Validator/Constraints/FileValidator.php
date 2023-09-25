<?php

namespace Wexample\SymfonyHelpers\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use function is_file;

class FileValidator extends \Symfony\Component\Validator\Constraints\FileValidator
{
    public function validate($value, Constraint $constraint)
    {
        // Some files may be missing when saving an entity,
        // especially in dev environment, so we allow saving non-existing ones.
        if (!is_file($value)) {
            return;
        }

        parent::validate($value, $constraint);
    }
}
