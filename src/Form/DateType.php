<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;

class DateType extends \Symfony\Component\Form\Extension\Core\Type\DateType
{
    use MaterializeFieldTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $this->materializeConfigureOptions($resolver);

        // We can't use a custom "widget" name as base date type will not support it.
        // So we use an extra option for it.
        $resolver->setDefault(AbstractForm::FIELD_OPTION_NAME_DATE_PICKER, null);
        $resolver->setDefault(AbstractForm::FIELD_OPTION_NAME_DATE_TYPE, 'date');

        $resolver->setDefault(
            'format',
            fn(
                Options $options,
                $value
            ) => ($options[AbstractForm::FIELD_OPTION_NAME_DATE_PICKER]) ? self::HTML5_FORMAT : $value
        );

        $resolver->setDefault(
            'widget',
            fn(
                Options $options,
                $value
            ) => ($options[AbstractForm::FIELD_OPTION_NAME_DATE_PICKER]) ? 'single_text' : $value
        );

        $resolver->setDefault(
            'html5',
            fn(
                Options $options,
                $value
            ) => ($options[AbstractForm::FIELD_OPTION_NAME_DATE_PICKER] && 'html5' === $options[AbstractForm::FIELD_OPTION_NAME_DATE_PICKER]) ? true : $value
        );
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        $view->vars['label_attr']['class'] ??= '';
        $view->vars['label_attr']['class'] = 'active';

        $classes = [];
        $view->vars[AbstractForm::FIELD_OPTION_NAME_DATE_PICKER] = $options[AbstractForm::FIELD_OPTION_NAME_DATE_PICKER];

        if ($view->vars[AbstractForm::FIELD_OPTION_NAME_DATE_PICKER]) {
            $view->vars[AbstractForm::FIELD_OPTION_NAME_DATE_TYPE] = $options[AbstractForm::FIELD_OPTION_NAME_DATE_TYPE];
            $options['widget'] = 'simple_text';
            $classes[] = AbstractForm::FIELD_OPTION_NAME_DATE_PICKER;
            $classes[] = AbstractForm::FIELD_OPTION_NAME_DATE_PICKER.'-'.
                $view->vars[AbstractForm::FIELD_OPTION_NAME_DATE_PICKER];
        }

        $this->materializeBuildView($view, $form, $options, $classes);
        parent::buildView($view, $form, $options);
    }
}
