<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use ReflectionClass;
use Symfony\Component\HttpKernel\KernelInterface;
use Wexample\SymfonyHelpers\Attribute\RectifiableEntity;

class ImportDtoRule extends AbstractRectifyRule
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
        if (! $rectifiableEntity->import) {
            return [];
        }

        $entityShortName = $entityReflection->getShortName();
        $dtoDir = $this->kernel->getProjectDir().'/src/Api/Dto/Entity/'.$entityShortName;
        $importDtoPath = $dtoDir.'/Import'.$entityShortName.'Dto.php';
        $importValidationDtoPath = $dtoDir.'/Import'.$entityShortName.'ValidationDto.php';

        if (! is_dir($dtoDir)) {
            mkdir($dtoDir, 0777, true);
        }

        if (! is_file($importDtoPath)) {
            $this->createImportDto(
                $entityShortName,
                $importDtoPath
            );
        }

        if (! is_file($importValidationDtoPath)) {
            $this->createImportValidationDto(
                $entityShortName,
                $importValidationDtoPath
            );
        }

        return [];
    }

    private function createImportDto(
        string $entityShortName,
        string $filePath
    ): void {
        $entityManipulatorTrait = $entityShortName.'EntityManipulatorTrait';

        $content = <<<PHP
<?php

namespace App\Api\Dto\Entity\\{$entityShortName};

use App\Api\Dto\Abstract\AbstractEntityImportDto;
use App\Entity\Traits\Manipulator\\{$entityManipulatorTrait};

class Import{$entityShortName}Dto extends AbstractEntityImportDto
{
    use {$entityManipulatorTrait};

    public static function getUniqueFieldName(): ?string
    {
        return null;
    }
}
PHP;

        file_put_contents($filePath, $content."\n");
    }

    private function createImportValidationDto(
        string $entityShortName,
        string $filePath
    ): void {
        $entityManipulatorTrait = $entityShortName.'EntityManipulatorTrait';

        $content = <<<PHP
<?php

namespace App\Api\Dto\Entity\\{$entityShortName};

use App\Api\Dto\Abstract\AbstractEntityImportValidationDto;
use App\Entity\Traits\Manipulator\\{$entityManipulatorTrait};

class Import{$entityShortName}ValidationDto extends AbstractEntityImportValidationDto
{
    use {$entityManipulatorTrait};
}
PHP;

        file_put_contents($filePath, $content."\n");
    }
}
