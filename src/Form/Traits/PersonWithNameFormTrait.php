<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use Symfony\Component\Form\FormBuilderInterface;
use Wexample\SymfonyHelpers\Form\AbstractForm;
use Wexample\SymfonyHelpers\Form\TextType;
use Wexample\SymfonyHelpers\Helper\IconMaterialHelper;

trait PersonWithNameFormTrait
{
    protected function builderAddPersonNames(
        FormBuilderInterface $builder
    ): AbstractForm {
        $builder
            ->add(
                'first_name',
                TextType::class,
                [
                    self::FIELD_OPTION_NAME_REQUIRED => false,
                    self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_TAG,
                ]
            )
            ->add(
                'last_name',
                TextType::class,
                [
                    self::FIELD_OPTION_NAME_REQUIRED => false,
                    self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_TAGS,
                ]
            );

        return $this;
    }
}
