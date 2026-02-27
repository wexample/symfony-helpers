<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use ReflectionClass;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Attribute\RectifiableEntity;

class ApiNormalizerRule extends AbstractRectifyRule
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
        $attributes = $entityReflection->getAttributes(RectifiableEntity::class);
        if ($attributes === []) {
            return [];
        }

        /** @var RectifiableEntity $rectifiableEntity */
        $rectifiableEntity = $attributes[0]->newInstance();
        if (! $rectifiableEntity->api) {
            return [];
        }

        $entityShortName = $entityReflection->getShortName();
        $normalizerDir = $this->kernel->getProjectDir().'/src/Api/Normalizer/Entity/'.$entityShortName;
        $normalizerPath = $normalizerDir.'/Default'.$entityShortName.'Normalizer.php';

        if (! is_file($normalizerPath)) {
            $this->createDefaultNormalizer(
                $entityShortName,
                $normalizerDir,
                $normalizerPath
            );
        }

        $controllerPath = $this->kernel->getProjectDir().'/src/Api/Controller/Entity/'.$entityShortName.'Controller.php';
        if (! is_file($controllerPath)) {
            $this->createDefaultController(
                $entityShortName,
                $controllerPath
            );
        }

        return [];
    }

    private function createDefaultNormalizer(
        string $entityShortName,
        string $normalizerDir,
        string $normalizerPath
    ): void {
        if (! is_dir($normalizerDir)) {
            mkdir($normalizerDir, 0777, true);
        }

        $entityManipulatorTrait = $entityShortName.'EntityManipulatorTrait';
        $entityNormalizerClass = 'Default'.$entityShortName.'Normalizer';

        $content = <<<PHP
<?php

namespace App\Api\Normalizer\Entity\\{$entityShortName};

use App\Entity\\{$entityShortName};
use App\Entity\Traits\Manipulator\\{$entityManipulatorTrait};
use ArrayObject;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Normalizer\AbstractEntityNormalizer;

class {$entityNormalizerClass} extends AbstractEntityNormalizer
{
    use {$entityManipulatorTrait};

    public function normalizeEntity(
        {$entityShortName}|AbstractEntity \$entity,
        ?string \$format = null,
        array \$context = []
    ): array|string|int|float|bool|ArrayObject|null {
        return parent::normalizeEntity(\$entity, \$format, \$context) + [
                'secureId' => \$entity->getSecureId(),
            ];
    }
}
PHP;

        file_put_contents($normalizerPath, $content."\n");
    }

    private function createDefaultController(
        string $entityShortName,
        string $controllerPath
    ): void {
        $entityManipulatorTrait = $entityShortName.'EntityManipulatorTrait';
        $normalizerClass = 'Default'.$entityShortName.'Normalizer';
        $repositoryClass = $entityShortName.'Repository';
        $controllerClass = $entityShortName.'Controller';
        $routePath = $this->toKebabCase($entityShortName);
        $routeName = $this->toSnakeCase($entityShortName);

        $content = <<<PHP
<?php

namespace App\Api\Controller\Entity;

use App\Api\Controller\Entity\Traits\DefaultListPaginatedApiControllerTrait;
use App\Api\Normalizer\Entity\\{$entityShortName}\\{$normalizerClass};
use App\Entity\Traits\Manipulator\\{$entityManipulatorTrait};
use App\Repository\\{$repositoryClass};
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyApi\Api\Controller\AbstractApiController;

#[Route(path: '{$routePath}/', name: '{$routeName}_')]
class {$controllerClass} extends AbstractApiController
{
    use {$entityManipulatorTrait};
    use DefaultListPaginatedApiControllerTrait;

    public function __construct(
        {$normalizerClass} \$normalizer,
        {$repositoryClass} \$repository
    )
    {
        \$this->defaultApiControllerNormalizer = \$normalizer;
        \$this->defaultApiControllerRepository = \$repository;
    }
}
PHP;

        file_put_contents($controllerPath, $content."\n");
    }

    private function toSnakeCase(
        string $value
    ): string {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }

    private function toKebabCase(
        string $value
    ): string {
        return str_replace('_', '-', $this->toSnakeCase($value));
    }
}
