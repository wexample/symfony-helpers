<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormBuilderInterface;

class StripePaymentForm extends AbstractForm
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        // No more token field,
        // We may not use a generic form for that ?
        $builder
            ->add(
                'submit',
                SubmitType::class,
                [
                    self::FIELD_OPTION_NAME_LABEL => 'label.pay_now',
                    self::FIELD_OPTION_NAME_IN_FOOTER => true,
                ]
            );
    }
}
