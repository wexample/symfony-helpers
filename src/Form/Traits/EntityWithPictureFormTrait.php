<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use Symfony\Component\Form\FormBuilderInterface;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Form\AbstractForm;
use Wexample\SymfonyHelpers\Form\PictureType;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait EntityWithPictureFormTrait
{
    use EntityWithFileFormTrait;

    protected function builderAddEntityPicture(
        FormBuilderInterface $builder,
        AbstractEntityInterface $entity,
        string $name,
        array $options = [],
        string $className = PictureType::class
    ): AbstractForm {
        $this->builderAddEntityFile(
            $builder,
            $entity,
            $name,
            array_merge(
                [
                    'preview_route' => implode(
                        '_',
                        [
                            ClassHelper::getTableizedName($entity),
                            TextHelper::toSnake($name),
                            'view',
                        ]
                    ),
                    'preview_options' => [
                        VariableHelper::ID => $entity->getId(),
                    ],
                ],
                $options
            ),
            $className
        );

        return $this;
    }
}
