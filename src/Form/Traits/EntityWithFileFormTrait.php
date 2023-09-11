<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use App\Wex\BaseBundle\Helper\ClassHelper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Form\AbstractForm;
use Wexample\SymfonyHelpers\Form\FileType;

trait EntityWithFileFormTrait
{
    protected ParameterBagInterface $parameterBag;

    protected function builderAddEntityFile(
        FormBuilderInterface $builder,
        AbstractEntityInterface $entity,
        string $name,
        array $options = [],
        string $className = FileType::class
    ): AbstractForm {
        $builder->add(
            $name,
            $className,
            array_merge(
                [
                    self::FIELD_OPTION_NAME_DATA => $this->entityFileInit(
                        $entity,
                        $name
                    ),
                ],
                $options
            )
        );

        return $this;
    }

    /**
     * Checks if the provided filename is associated to a real file and return
     * it.
     */
    public function entityFileInit(
        $entity,
        string $name,
        ?string $dir = null
    ): ?File {
        return $this->checkFile(
            $entity,
            $name,
            ClassHelper::getTableizedName($entity),
            $dir
        );
    }

    private function checkFile(
        $entity,
        string $name,
        string $tableized,
        ?string $dir
    ): ?File {
        $fileName = ClassHelper::getFieldGetterValue($entity, $name);

        if ($fileName) {
            if (!$dir) {
                $dir = $this
                        ->getParameterBag()
                        ->get(
                            $tableized.'_'.$name.'_dir'
                        ).'/';
            }

            $filePath = $dir.$fileName;

            return file_exists($filePath) ? new File(
                $filePath
            ) : null;
        }

        return null;
    }

    protected function getParameterBag(): ParameterBagInterface
    {
        return $this->parameterBag;
    }
}
