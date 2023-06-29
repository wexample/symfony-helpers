<?php

namespace Wexample\SymfonyHelpers\Twig;

use Wexample\SymfonyHelpers\Helper\VariableHelper;

abstract class AbstractExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @var string
     */
    protected const FUNCTION_OPTION_IS_SAFE = 'is_safe';

    protected const FUNCTION_OPTION_IS_SAFE_VALUE_HTML = [self::FUNCTION_OPTION_HTML];

    /**
     * @var string
     */
    protected const FUNCTION_OPTION_HTML = VariableHelper::HTML;

    /**
     * @var string
     */
    protected const FUNCTION_OPTION_NEEDS_CONTEXT = 'needs_context';

    /**
     * @var string
     */
    protected const FUNCTION_OPTION_NEEDS_ENVIRONMENT = 'needs_environment';
}
