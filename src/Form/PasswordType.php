<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;

class PasswordType extends \Symfony\Component\Form\Extension\Core\Type\PasswordType
{
    use MaterializeFieldTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->materializeConfigureOptions($resolver);
        $resolver->setDefaults(
            [
                AbstractForm::FIELD_OPTION_NAME_SHOW_BUTTON => null,
            ]
        );
        parent::configureOptions($resolver);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars[AbstractForm::FIELD_OPTION_NAME_SHOW_BUTTON] = isset($options[AbstractForm::FIELD_OPTION_NAME_SHOW_BUTTON]);
        $this->materializeBuildView($view, $form, $options);
        parent::buildView($view, $form, $options);
    }
}
