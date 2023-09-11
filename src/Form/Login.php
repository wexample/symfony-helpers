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

class Login extends AbstractForm
{
    public string $formName = 'login';

    public static bool $ajax = true;

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    self::FIELD_OPTION_NAME_LABEL => 'label.login',
                    self::FIELD_OPTION_NAME_ATTR => [],
                    self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_FACE,
                    self::FIELD_OPTION_NAME_REQUIRED => false,
                    self::FIELD_OPTION_NAME_MAPPED => false,
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    self::FIELD_OPTION_NAME_LABEL => 'label.password',
                    self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_LOCK,
                    self::FIELD_OPTION_NAME_SHOW_BUTTON => true,
                    self::FIELD_OPTION_NAME_REQUIRED => false,
                    self::FIELD_OPTION_NAME_MAPPED => false,
                ]
            )
            ->add(
                'remember_me',
                CheckboxType::class,
                [
                    self::FIELD_OPTION_NAME_LABEL => 'security.login.remember_me',
                    self::FIELD_OPTION_NAME_FILLED => true,
                    self::FIELD_OPTION_NAME_REQUIRED => false,
                    self::FIELD_OPTION_NAME_MAPPED => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    self::FIELD_OPTION_NAME_LABEL => 'security.submit.connexion',
                ]
            );
    }
}
