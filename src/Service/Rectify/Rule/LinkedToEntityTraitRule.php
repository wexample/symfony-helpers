<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use ReflectionClass;
use Symfony\Component\HttpKernel\KernelInterface;

class LinkedToEntityTraitRule extends AbstractRectifyRule
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
        $traitShortName = 'LinkedTo'.$entityShortName.'Trait';
        $traitClass = 'App\\Entity\\Traits\\'.$traitShortName;
        $traitPath = $this->buildPathFromClassName($traitClass);

        if (is_file($traitPath)) {
            return [];
        }

        $traitDir = dirname($traitPath);
        if (! is_dir($traitDir)) {
            mkdir($traitDir, 0777, true);
        }

        $propertyName = lcfirst($entityShortName);
        $propertyColumn = $this->toSnakeCase($propertyName).'_id';
        $entityClass = $entityReflection->getName();

        $content = <<<PHP
<?php

namespace App\Entity\Traits;

use {$entityClass};
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

trait {$traitShortName}
{
    #[ManyToOne(targetEntity: {$entityShortName}::class)]
    #[JoinColumn(name: "{$propertyColumn}", nullable: false)]
    protected ?{$entityShortName} \${$propertyName} = null;

    public function get{$entityShortName}(): ?{$entityShortName}
    {
        return \$this->{$propertyName};
    }

    public function set{$entityShortName}(?{$entityShortName} \${$propertyName}): static
    {
        \$this->{$propertyName} = \${$propertyName};

        return \$this;
    }
}
PHP;

        file_put_contents($traitPath, $content."\n");

        return [];
    }

    private function buildPathFromClassName(
        string $className
    ): string {
        return $this->kernel->getProjectDir().'/src/'.str_replace('\\', '/', str_replace('App\\', '', $className)).'.php';
    }

    private function toSnakeCase(
        string $value
    ): string {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }
}
