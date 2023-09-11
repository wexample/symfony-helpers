<?php

namespace Wexample\SymfonyHelpers\Form\FormError;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Wexample\SymfonyHelpers\Form\AbstractForm;

class FormErrorTranslated extends FormError
{
    public function __construct(
        ?string $message,
        FormInterface $form,
        string $messageTemplate = null,
        array $messageParameters = [],
        int $messagePluralization = null,
        $cause = null
    ) {
        $message = AbstractForm::transForm(
            $message,
            $form
        );

        parent::__construct(
            $message,
            $messageTemplate,
            $messageParameters,
            $messagePluralization,
            $cause
        );
    }
}
