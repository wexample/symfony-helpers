<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use Doctrine\ORM\Mapping\Entity;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\HttpKernel\KernelInterface;

class EntityManipulatorTraitRule extends AbstractRectifyRule
{
    public function __construct(
        private readonly KernelInterface $kernel
    ) {
    }

    /**
     * @return string[]
     */
    public function apply(
        ReflectionClass $entityReflection
    ): array {
        $entityShortName = $entityReflection->getShortName();
        $traitShortName = $entityShortName.'EntityManipulatorTrait';
        $traitClass = 'App\\Entity\\Traits\\Manipulator\\'.$traitShortName;

        $this->ensureManipulatorTraitExists(
            $traitClass,
            $traitShortName,
            $entityReflection->getName()
        );

        $repositoryClass = $this->resolveRepositoryClass($entityReflection);
        if (! class_exists($repositoryClass)) {
            return [];
        }

        $this->ensureRepositoryUsesManipulatorTrait(
            $repositoryClass,
            $traitClass,
            $traitShortName
        );

        return [];
    }

    private function ensureManipulatorTraitExists(
        string $traitClass,
        string $traitShortName,
        string $entityClass
    ): void {
        if (trait_exists($traitClass)) {
            return;
        }

        $traitPath = $this->buildPathFromClassName($traitClass);
        $traitDir = dirname($traitPath);
        if (! is_dir($traitDir)) {
            mkdir($traitDir, 0777, true);
        }

        $content = <<<PHP
<?php

namespace App\Entity\Traits\Manipulator;

use {$entityClass};
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;

trait {$traitShortName}
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return {$entityClass}::class;
    }
}
PHP;

        file_put_contents($traitPath, $content."\n");
    }

    private function ensureRepositoryUsesManipulatorTrait(
        string $repositoryClass,
        string $traitClass,
        string $traitShortName
    ): void {
        $repositoryReflection = new ReflectionClass($repositoryClass);
        $repositoryPath = $repositoryReflection->getFileName();
        $content = file_get_contents($repositoryPath);

        if (! str_contains($content, 'use '.$traitClass.';')) {
            $content = preg_replace(
                '/^namespace\s+[^;]+;\n/m',
                "$0\nuse ".$traitClass.";\n",
                $content,
                1
            );
        }

        if (! preg_match('/^\s*use\s+'.preg_quote($traitShortName, '/').';\s*$/m', $content)) {
            $repositoryShortName = $repositoryReflection->getShortName();
            $content = preg_replace(
                '/(class\s+'.preg_quote($repositoryShortName, '/').'\b[^{]*\{)\n/m',
                "$1\n    use ".$traitShortName.";\n",
                $content,
                1
            );
        }

        file_put_contents($repositoryPath, $content);
    }

    private function resolveRepositoryClass(
        ReflectionClass $entityReflection
    ): string {
        $entityAttributes = $entityReflection->getAttributes(
            Entity::class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        if ($entityAttributes !== []) {
            /** @var Entity $entityAttribute */
            $entityAttribute = $entityAttributes[0]->newInstance();
            if ($entityAttribute->repositoryClass) {
                return $entityAttribute->repositoryClass;
            }
        }

        return sprintf(
            'App\\Repository\\%sRepository',
            $entityReflection->getShortName()
        );
    }

    private function buildPathFromClassName(
        string $className
    ): string {
        return $this->kernel->getProjectDir().'/src/'.str_replace('\\', '/', str_replace('App\\', '', $className)).'.php';
    }
}
