<?php

namespace Wexample\SymfonyHelpers\Form;

class SwitchType extends CheckboxType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'switch';
    }
}
