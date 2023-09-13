<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\AbstractType;

trait DefaultTypeTrait
{
    public function materializeConfigureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                AbstractType::FIELD_OPTION_NAME_LABEL => true,
            ]
        );
    }
}
