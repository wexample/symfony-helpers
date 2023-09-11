<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;

class FloatType extends AbstractType
{
    use MaterializeFieldTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->materializeConfigureOptions($resolver);

        parent::configureOptions($resolver);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['value'] = $view->vars['data'];

        $this->materializeBuildView($view, $form, $options);
        parent::buildView($view, $form, $options);
    }

    public function getParent()
    {
        return NumberType::class;
    }
}
