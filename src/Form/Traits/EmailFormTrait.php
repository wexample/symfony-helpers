<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use App\Wex\BaseBundle\Helper\VariableHelper;
use Symfony\Component\Form\FormBuilderInterface;
use Wexample\SymfonyHelpers\Form\AbstractForm;
use Wexample\SymfonyHelpers\Form\EmailType;
use Wexample\SymfonyHelpers\Helper\IconMaterialHelper;

trait EmailFormTrait
{
    public function builderAddEmail(
        FormBuilderInterface $builder,
        array $options = [],
        string $child = VariableHelper::EMAIL
    ): self {
        $builder->add(
            $child,
            EmailType::class,
            $this->resolveOptions([
                AbstractForm::FIELD_OPTION_NAME_REQUIRED => false,
                AbstractForm::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_EMAIL,
            ], $options)
        );

        return $this;
    }
}
