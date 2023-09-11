<?php

namespace Wexample\SymfonyHelpers\Form;

use HTMLPurifier;
use HTMLPurifier_Config;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;

class HtmlType extends TextareaType
{
    use MaterializeFieldTypeTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        $this->materializeConfigureOptions($resolver);

        $resolver->setDefaults(
            [
                AbstractForm::FIELD_OPTION_NAME_SANITIZE => true,
            ]
        );

        parent::configureOptions($resolver);
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->addModelTransformer(
                new CallbackTransformer(
                    fn(?string $text) => $text,
                    function (?string $text) {
                        // Prevent hacky html.
                        $config = HTMLPurifier_Config::createDefault();
                        $purifier = new HTMLPurifier($config);

                        return $purifier->purify($text);
                    },
                )
            );
    }
}
