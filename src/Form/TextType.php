<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\StringTypeTrait;

class TextType extends \Symfony\Component\Form\Extension\Core\Type\TextType
{
    use StringTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->materializeConfigureOptions($resolver);

        $resolver->setDefaults(
            [
                AbstractForm::FIELD_OPTION_NAME_CHARACTER_COUNTER => null,
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

        if ($options[AbstractForm::FIELD_OPTION_NAME_CHARACTER_COUNTER]) {
            $classes[] = 'field-character-counted';
            $view->vars[AbstractForm::FIELD_OPTION_NAME_CHARACTER_COUNTER] = $options[AbstractForm::FIELD_OPTION_NAME_CHARACTER_COUNTER];
        }

        $this->stringRestrictLength($view, $form);
        $this->materializeBuildView($view, $form, $options, $classes);
        parent::buildView($view, $form, $options);
    }
}
