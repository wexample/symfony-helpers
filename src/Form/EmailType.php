<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;

class EmailType extends \Symfony\Component\Form\Extension\Core\Type\EmailType
{
    use MaterializeFieldTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->materializeConfigureOptions($resolver);
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'constraints' => [new Email()],
        ]);
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        $this->materializeBuildView($view, $form, $options);
        parent::buildView($view, $form, $options);
    }
}
