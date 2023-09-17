<?php

namespace Wexample\SymfonyHelpers\Helper;

class FormHelper
{
    public static function buildRoute(string $formClassName): string
    {
        return substr(
            \Wexample\SymfonyHelpers\Helper\ClassHelper::getTableizedName(
                $formClassName
            ),
            0,
            -strlen(
                '_'.\Wexample\SymfonyHelpers\Helper\ClassHelper::CLASS_PATH_PART_FORM
            )
        );
    }
}
