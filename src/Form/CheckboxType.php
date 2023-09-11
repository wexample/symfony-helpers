<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;

class CheckboxType extends \Symfony\Component\Form\Extension\Core\Type\CheckboxType
{
    use MaterializeFieldTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->materializeConfigureOptions($resolver);

        $resolver->setDefaults(
            [
                AbstractForm::FIELD_OPTION_NAME_FILLED => false,
            ]
        );
        parent::configureOptions($resolver);
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        $classes = [];
        if ($options[AbstractForm::FIELD_OPTION_NAME_FILLED]) {
            $classes[] = 'filled-in';
        }

        $this->materializeBuildView($view, $form, $options, $classes);

        unset($view->vars['icon']);

        parent::buildView($view, $form, $options);
    }
}
