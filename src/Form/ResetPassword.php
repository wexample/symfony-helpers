<?php
/**
 * Created by PhpStorm.
 * User: weeger
 * Date: 02/02/19
 * Time: 15:23.
 */

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Wexample\SymfonyHelpers\Helper\IconMaterialHelper;

class ResetPassword extends AbstractForm
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    self::FIELD_OPTION_NAME_LABEL => 'security.label.resetting',
                    self::FIELD_OPTION_NAME_ATTR => [
                        self::FIELD_OPTION_VALUE_ATTR_PLACEHOLDER => 'security.login.placeholder',
                    ],
                    self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_FACE,
                    self::FIELD_OPTION_NAME_REQUIRED => false,
                    self::FIELD_OPTION_NAME_MAPPED => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    self::FIELD_OPTION_NAME_LABEL => 'security.submit.resetting',
                ]
            );
    }
}
