<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\StringTypeTrait;

class UrlType extends \Symfony\Component\Form\Extension\Core\Type\UrlType
{
    use StringTypeTrait;

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
        // Enforce to use an "url" field type.
        // @see https://github.com/symfony/symfony/issues/30635#issuecomment-476154888
        $options['default_protocol'] = null;

        $this->stringRestrictLength($view, $form);
        $this->materializeBuildView($view, $form, $options);
        parent::buildView($view, $form, $options);
    }
}
