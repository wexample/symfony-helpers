<?php

namespace Wexample\SymfonyHelpers\Helper;

use Wexample\Helpers\Helper\ClassHelper;

class FormHelper
{
    public static function buildRoute(string $formClassName): string
    {
        return substr(
            ClassHelper::getTableizedName(
                $formClassName
            ),
            0,
            -strlen(
                '_'.ClassHelper::CLASS_PATH_PART_FORM
            )
        );
    }
}
