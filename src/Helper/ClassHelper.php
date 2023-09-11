<?php

namespace Wexample\SymfonyHelpers\Helper;

use Doctrine\Common\Util\ClassUtils;
use Exception;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use function array_map;
use function array_slice;
use function count;
use function explode;
use function implode;
use function is_string;
use function lcfirst;
use function str_replace;
use function strlen;
use function substr;

class ClassHelper
{
    final public const PATH_SEPARATOR = '\\';

    final public const CLASS_APP_BASE_PATH =
        self::CLASS_PATH_PART_APP
        .self::NAMESPACE_SEPARATOR;

    final public const CLASS_API_BASE_PATH =
        self::CLASS_APP_BASE_PATH
        .self::CLASS_PATH_PART_API
        .self::NAMESPACE_SEPARATOR;

    final public const CLASS_ENTITY_BASE_PATH =
        self::CLASS_APP_BASE_PATH
        .self::CLASS_PATH_PART_ENTITY
        .self::NAMESPACE_SEPARATOR;

    final public const CLASS_TEST_BASE_PATH =
        self::CLASS_APP_BASE_PATH
        .self::CLASS_PATH_PART_TESTS
        .self::NAMESPACE_SEPARATOR;

    final public const CLASS_SERVICE_BASE_PATH =
        self::CLASS_APP_BASE_PATH
        .self::CLASS_PATH_PART_SERVICE
        .self::NAMESPACE_SEPARATOR;

    final public const CLASS_FORM_BASE_PATH =
        self::CLASS_APP_BASE_PATH
        .self::CLASS_PATH_PART_FORM
        .self::NAMESPACE_SEPARATOR;

    final public const CLASS_FORM_PROCESSOR_BASE_PATH =
        self::CLASS_SERVICE_BASE_PATH
        .self::CLASS_PATH_PART_FORM_PROCESSOR
        .self::NAMESPACE_SEPARATOR;

    final public const CLASS_PATH_PART_APP = 'App';

    final public const CLASS_PATH_PART_CONTROLLER = 'Controller';

    final public const CLASS_PATH_PART_API = 'Api';

    final public const CLASS_PATH_PART_ENTITY = 'Entity';

    final public const CLASS_PATH_PART_FORM = 'Form';

    final public const CLASS_PATH_PART_FORM_PROCESSOR = self::CLASS_PATH_PART_FORM.'Processor';

    final public const CLASS_PATH_PART_SERVICE = 'Service';

    final public const CLASS_PATH_PART_TESTS = 'Tests';

    final public const CLASS_TYPE_CLASS = 'class';

    final public const CLASS_TYPE_TRAIT = 'trait';

    final public const CLASS_TYPE_INTERFACE = 'interface';

    final public const NAMESPACE_SEPARATOR = '\\';

    final public const METHOD_SEPARATOR = '::';

    final public const DIR_SRC = 'src'.FileHelper::FOLDER_SEPARATOR;

    final public const DIR_TESTS = 'tests'.FileHelper::FOLDER_SEPARATOR;

    final public const PHP_OPENER = '<?php';

    final public const AUTOLOAD_DIRS = [
        ClassHelper::CLASS_APP_BASE_PATH => ClassHelper::DIR_SRC,
        ClassHelper::CLASS_TEST_BASE_PATH => ClassHelper::DIR_TESTS,
    ];

    public static function getKebabName($className): string
    {
        return str_replace(
            '_',
            '-',
            static::getTableizedName($className)
        );
    }

    public static function getTableizedName(object|string $className): string
    {
        return TextHelper::toSnake(static::getShortName($className));
    }

    public static function getShortName(object|string $className): string
    {
        $className = ClassHelper::getRealClassPath($className);

        if (class_exists($className)) {
            try {
                $reflexion = new ReflectionClass($className);

                return $reflexion->getShortName();
            } catch (Exception) {
            }
        }

        return TextHelper::getLastChunk(
            $className,
            ClassHelper::NAMESPACE_SEPARATOR,
        );
    }

    public static function getRealClassPath(object|string $entity): string
    {
        return ClassUtils::getRealClass(
            self::getClassPath($entity)
        );
    }

    public static function getClassPath(object|string $entity): string
    {
        $classPath = is_string($entity) ? $entity : $entity::class;

        if (str_contains($classPath, self::METHOD_SEPARATOR)) {
            return TextHelper::getFirstChunk(
                $classPath,
                self::METHOD_SEPARATOR,
            );
        }

        return $classPath;
    }

    public static function getCousin(
        object|string $className,
        string $classBasePath,
        string $classSuffix,
        string $cousinBasePath,
        string $cousinSuffix = ''
    ): string {
        $parts = static::getPathParts(
            $className,
            count(explode('\\', $classBasePath)) - 1
        );

        $classBase = implode('\\', $parts);

        // Remove suffix if exists.
        $classBase = $classSuffix ? substr(
            $classBase,
            0,
            -strlen($classSuffix)
        ) : $classBase;

        return $cousinBasePath.$classBase.$cousinSuffix;
    }

    public static function getPathParts(
        object|string $type,
        $offset = 2
    ): array {
        return array_slice(
            explode(
                '\\',
                self::getRealClassPath($type)
            ),
            $offset
        );
    }

    /**
     * Return a someThingName form a \App\Entity\SomeThingName.
     * Used to find a field name have a relation to an entity.
     */
    public static function getFieldName(string $className): string
    {
        return lcfirst(static::getShortName($className));
    }

    public static function applyPropertiesSetters(
        object $target,
        array $properties
    ): void {
        foreach ($properties as $fieldName => $value) {
            ClassHelper::setFieldSetterValue(
                $target,
                $fieldName,
                $value
            );
        }
    }

    public static function setFieldSetterValue(
        object $object,
        string $fieldName,
        $fieldValue
    ) {
        $method = 'set'.TextHelper::toClass($fieldName);

        return $object->$method($fieldValue);
    }

    public static function getFieldGetterValue(
        object $object,
        string $fieldName
    ) {
        $method = 'get'.TextHelper::toClass($fieldName);

        return $object->$method();
    }

    public static function longTableizedNameToClass(string $name): string
    {
        $exp = explode('-', $name);
        $exp = array_map(TextHelper::class.'::toClass', $exp);

        return implode('\\', $exp);
    }

    public static function longTableized(
        object|string $name,
        string $separator = '-'
    ): string {
        $parts = ClassHelper::getPathParts($name);
        $parts = array_map(TextHelper::class.'::toSnake', $parts);

        return implode(
            $separator,
            $parts
        );
    }

    public static function longTableizedToPath(string $name): string
    {
        $exp = explode('-', $name);
        $exp = array_map(TextHelper::class.'::stringToKebab', $exp);

        return implode('/', $exp);
    }

    public static function fullEntityClassPathFromEntityPath(
        string $entityPath,
        bool $ifExists = true
    ): ?string {
        $entityPathPath = self::CLASS_ENTITY_BASE_PATH.TextHelper::toClass($entityPath);

        if (!$ifExists || class_exists($entityPathPath)) {
            return $entityPathPath;
        }

        return null;
    }

    public static function buildClassFilePath(
        string $className,
        string $folder = null
    ): string {
        $parts = explode(self::NAMESPACE_SEPARATOR, $className);

        $parts = array_slice(
            $parts,
            count(
                ClassHelper::splitNamespace(
                    self::getAutoloadNamespace($className)
                )
            ) - 1
        );

        return ($folder ?: self::getAutoloadFolder($className)).implode(
                FileHelper::FOLDER_SEPARATOR,
                $parts
            ).FileHelper::EXTENSION_SEPARATOR.FileHelper::FILE_EXTENSION_PHP;
    }

    public static function splitNamespace(string $classPath): array
    {
        return explode(ClassHelper::NAMESPACE_SEPARATOR, $classPath);
    }

    public static function getAutoloadNamespace(string $classPath): string
    {
        $parts = explode(self::NAMESPACE_SEPARATOR, $classPath);
        $candidate = [];

        foreach (ClassHelper::AUTOLOAD_DIRS as $namespace => $dir) {
            $namespaceParts = explode(self::NAMESPACE_SEPARATOR, $namespace);
            $intersect = array_intersect($namespaceParts, $parts);

            if (count($intersect) > count($candidate)) {
                $candidate = $intersect;
            }
        }

        return implode(self::NAMESPACE_SEPARATOR, $candidate)
            .self::NAMESPACE_SEPARATOR;
    }

    public static function getAutoloadFolder(string $classPath): string
    {
        $namespace = ClassHelper::getAutoloadNamespace($classPath);

        return ClassHelper::AUTOLOAD_DIRS[$namespace] ?? ClassHelper::DIR_SRC;
    }

    public static function buildClassNameFromRealPath(
        string $realPath,
        string $projectDir,
    ): string {
        $className = substr(
            $realPath,
            strlen($projectDir.self::DIR_SRC),
            -strlen('.'.pathinfo($realPath)['extension'])
        );

        return 'App'
            .str_replace(
                '/',
                self::NAMESPACE_SEPARATOR,
                $className
            );
    }

    public static function trimFirstClassChunk(
        string $path
    ): string {
        return TextHelper::trimFirstChunk(
            $path,
            ClassHelper::NAMESPACE_SEPARATOR
        );
    }

    public static function trimLastClassChunk(
        string $path
    ): string {
        return TextHelper::trimLastChunk(
            $path,
            ClassHelper::NAMESPACE_SEPARATOR
        );
    }

    public static function join(
        array $parts,
        bool $startSeparator = false,
        bool $endSeparator = false,
    ): string {
        return ($startSeparator ? ClassHelper::NAMESPACE_SEPARATOR : '')
            .implode(
                ClassHelper::NAMESPACE_SEPARATOR,
                $parts
            ).($endSeparator ? ClassHelper::NAMESPACE_SEPARATOR : '');
    }

    public static function isClassPath(
        null|object|string $class,
        string $className
    ): bool {
        return $class && ClassHelper::getRealClassPath($class) === $className;
    }

    /**
     * @throws ReflectionException[]
     */
    public static function getAllMethodsWithChildrenAttribute(
        string $class,
        string $attributeClass
    ): array {
        $methods = [];

        try {
            $reflexion = new ReflectionClass($class);
        } catch (Exception) {
            return [];
        }

        foreach ($reflexion->getMethods() as $method) {
            if (self::getChildrenAttributes(
                $class.self::METHOD_SEPARATOR.$method->getName(),
                $attributeClass
            )) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    /**
     * @return ReflectionAttribute[]
     */
    public static function getChildrenAttributes(
        ReflectionMethod|ReflectionClass|string $subjectPath,
        string $attributeClass
    ): array {
        if (is_string($subjectPath)) {
            if (str_contains($subjectPath, ClassHelper::METHOD_SEPARATOR)) {
                try {
                    $reflexion = new ReflectionMethod($subjectPath);
                } catch (Exception) {
                    return [];
                }
            } elseif (class_exists($subjectPath)) {
                $reflexion = new ReflectionClass($subjectPath);
            } else {
                return [];
            }
        } else {
            $reflexion = $subjectPath;
        }

        if (trait_exists($attributeClass)) {
            $attributes = $reflexion->getAttributes();
            $output = [];

            foreach ($attributes as $attribute) {
                if (ClassHelper::classUsesTrait($attribute->getName(), $attributeClass)) {
                    $output[] = $attribute;
                }
            }

            return $output;
        }

        return $reflexion->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF);
    }

    public static function classUsesTrait(
        string|object $class,
        string $trait
    ): bool {
        return in_array(
            $trait,
            self::classUsagesRecursive($class)
        );
    }

    public static function classUsagesRecursive(string|object $class): array
    {
        if (is_object($class)) {
            $class = $class::class;
        }

        $results = [];

        foreach (array_reverse(class_parents($class)) + [$class => $class] as $class) {
            $results += self::traitUsageRecursive($class);
        }

        return array_unique($results);
    }

    public static function traitUsageRecursive($trait): array
    {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += self::traitUsageRecursive($trait);
        }

        return $traits;
    }

    public static function buildPathFromClassName(string $className): string
    {
        return FileHelper::joinPathParts(
            static::getPathParts($className)
        );
    }

    public static function buildClassNameFromPath(
        string $path,
        string $classPathPrefix = '',
        string $classPathSuffix = ''
    ): string {
        $pathParts = explode(
            FileHelper::FOLDER_SEPARATOR,
            rtrim(
                $path,
                FileHelper::FOLDER_SEPARATOR
            )
        );

        foreach ($pathParts as $key => $part) {
            $pathParts[$key] = TextHelper::toClass($part);
        }

        return $classPathPrefix.implode(
                ClassHelper::NAMESPACE_SEPARATOR,
                $pathParts
            )
            .$classPathSuffix;
    }

    public static function classImplementsInterface(
        string|object $class,
        string $interface
    ): bool {
        $interfaces = class_implements($class);

        if (isset($interfaces[$interface])) {
            return true;
        }

        return false;
    }

    public static function callMethodIfExists(
        object $object,
        string $methodName,
        array $arguments = []
    ): mixed {
        // Check if the method exists in the object
        if (method_exists($object, $methodName)) {
            // Call the method with arguments
            return call_user_func_array([$object, $methodName], $arguments);
        } else {
            // Throw an exception if the method doesn't exist
            throw new Exception('Method '.$methodName.' not found in '.get_class($object));
        }

        return null;
    }
}
