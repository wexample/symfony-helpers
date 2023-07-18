<?php

namespace Wexample\SymfonyHelpers\Service\Syntax;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;
use Wexample\SymfonyHelpers\Helper\BundleHelper;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\FileHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

abstract class AbstractSyntaxService
{
    protected readonly string $projectDir;

    public function __construct(
        KernelInterface $kernel,
        protected readonly Environment $twig,
    ) {
        $this->projectDir = $kernel->getProjectDir();
    }

    public function writeCousinIfMissingByName(
        string|object $classPath,
        string $classBasePath,
        string $cousinName
    ): void {
        $classPath = ClassHelper::getRealClassPath($classPath);
        $cousinClassPath = static::getCousinPathByName($classPath, $cousinName);
        $cousinParams = $this->getCousinParameters($cousinName);
        $cousinSuffix = $cousinParams[VariableHelper::SUFFIX];
        $cousinFolder = $cousinParams[VariableHelper::FOLDER] ?? BundleHelper::FOLDER_SRC;

        $this->writeCousinIfMissing(
            $classPath,
            $classBasePath,
            $cousinClassPath,
            $cousinSuffix,
            $cousinFolder
        );
    }

    public static function getCousinPathByName(
        string|object $classPath,
        string $cousinName
    ): string {
        $cousinParams = static::getCousinParameters($cousinName);
        $cousinBasePath = $cousinParams[VariableHelper::BASE_PATH];
        $cousinSuffix = $cousinParams[VariableHelper::SUFFIX];

        return ClassHelper::getCousin(
            $classPath,
            ClassHelper::CLASS_ENTITY_BASE_PATH,
            '',
            $cousinBasePath,
            $cousinSuffix
        );
    }

    abstract public static function getCousinParameters(string $cousinName): ?array;

    public function writeCousinIfMissing(
        string|object $classPath,
        string|object $classBasePath,
        string $cousinClassPath,
        string $cousinSuffix,
        string $cousinFolder,
        array $parameters = []
    ) {
        if (
            (
                // Work with classes, and this is a class.
                (ClassHelper::CLASS_TYPE_CLASS === $this->getClassType() && class_exists($classPath))
                // Work with traits, and this is a trait.
                || (ClassHelper::CLASS_TYPE_TRAIT === $this->getClassType() && trait_exists($classPath))
            )
            // Destination does not exist, can be any type of object.
            && (!trait_exists($cousinClassPath) || !class_exists($cousinClassPath))) {
            $classShortName = ClassHelper::getShortName($classPath);

            $relativeClassPath = TextHelper::trimString(
                $classPath,
                $classBasePath,
                $classShortName
            );

            $cousinNamespace = ClassHelper::trimLastClassChunk($cousinClassPath);

            $fileDestination = $this->projectDir
                .FileHelper::FOLDER_SEPARATOR
                .ClassHelper::buildClassFilePath(
                    $cousinClassPath,
                    $cousinFolder
                );

            $fileContent =
                ClassHelper::PHP_OPENER
                .PHP_EOL
                .$this->twig->render(
                    'php/'.$this->getGroup().'/'.$cousinSuffix.'.html.twig',
                    array_merge([
                        'classPath' => $classPath,
                        'className' => $classShortName,
                        'cousinNamespace' => $cousinNamespace,
                        'entityRelativeClassPath' => $relativeClassPath,
                    ], $parameters)
                );

            FileHelper::createFileIfMissing(
                $fileDestination,
                $fileContent
            );
        }
    }

    public static function getClassType(): string
    {
        return ClassHelper::CLASS_TYPE_CLASS;
    }

    abstract public static function getGroup(): string;
}
