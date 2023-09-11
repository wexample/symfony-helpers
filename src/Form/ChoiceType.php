<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;

class ChoiceType extends \Symfony\Component\Form\Extension\Core\Type\ChoiceType
{
    use MaterializeFieldTypeTrait;

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
        $this->materializeBuildView($view, $form, $options);

        // Active label moves it from over select field.
        $view->vars['label_attr']['class'] = 'active';

        parent::buildView($view, $form, $options);
    }
}
