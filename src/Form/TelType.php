<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\StringTypeTrait;

class TelType extends \Symfony\Component\Form\Extension\Core\Type\TelType
{
    use StringTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->materializeConfigureOptions($resolver);
        parent::configureOptions($resolver);
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        $this->stringRestrictLength($view, $form);
        $this->materializeBuildView($view, $form, $options);
        parent::buildView($view, $form, $options);
    }
}
