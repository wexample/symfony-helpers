<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;

class SubmitType extends \Symfony\Component\Form\Extension\Core\Type\SubmitType
{
    use MaterializeFieldTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->materializeConfigureOptions($resolver);
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            AbstractForm::FIELD_OPTION_NAME_VALIDATE => false,
        ]);
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        if (!isset($view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR]['class'])) {
            $view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR]['class'] = 'btn right';
        }

        $this->materializeBuildView($view, $form, $options);
        parent::buildView($view, $form, $options);
    }
}
