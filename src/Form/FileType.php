<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;

class FileType extends \Symfony\Component\Form\Extension\Core\Type\FileType
{
    use MaterializeFieldTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                AbstractForm::FIELD_OPTION_NAME_MULTIPLE => false,
                AbstractForm::FIELD_OPTION_NAME_DRAG_ZONE => false,
            ]
        );

        $this->materializeConfigureOptions($resolver);
        parent::configureOptions($resolver);
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        $this->materializeBuildView($view, $form, $options);

        $view->vars[AbstractForm::FIELD_OPTION_NAME_DRAG_ZONE] = $options[AbstractForm::FIELD_OPTION_NAME_DRAG_ZONE] ?? '';
        $view->vars[AbstractForm::FIELD_OPTION_NAME_MULTIPLE] = $options[AbstractForm::FIELD_OPTION_NAME_MULTIPLE] ?? null;

        parent::buildView($view, $form, $options);
    }
}
