<?php

namespace Wexample\SymfonyHelpers\Helper;

class BundleHelper
{
    final public const AUTHOR_COMPANY = 'Wexample';
    final public const ALIAS_PREFIX = '@';
    final public const BUNDLE_PATH_RESOURCES = 'Resources'.FileHelper::FOLDER_SEPARATOR;
    final public const BUNDLE_PATH_TEMPLATES = self::BUNDLE_PATH_RESOURCES.self::DIR_TEMPLATES;
    final public const CLASS_PATH_PREFIX = ClassHelper::NAMESPACE_SEPARATOR.'App'.ClassHelper::NAMESPACE_SEPARATOR;
    final public const DIR_SRC = self::FOLDER_SRC.FileHelper::FOLDER_SEPARATOR;
    final public const DIR_TEMPLATES = 'templates'.FileHelper::FOLDER_SEPARATOR;
    final public const FOLDER_SRC = 'src';
}
