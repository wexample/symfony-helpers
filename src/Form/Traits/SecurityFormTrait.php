<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use App\Wex\BaseBundle\Helper\EntityHelper;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Wexample\SymfonyHelpers\Entity\User;
use Wexample\SymfonyHelpers\Form\AbstractForm;
use Wexample\SymfonyHelpers\Form\PasswordType;
use Wexample\SymfonyHelpers\Helper\IconMaterialHelper;
use function array_merge;
use function reset;

trait SecurityFormTrait
{
    public function builderAddPassword(
        FormBuilderInterface $builder,
        array $options = []
    ) {
        // Copied from FOS Security bundle form definition.

        $constraintsOptions = [
            'message' => 'fos_user.current_password.invalid',
        ];

        if (!empty($options['validation_groups'])) {
            $constraintsOptions['groups'] = [
                reset(
                    $options['validation_groups']
                ),
            ];
        }

        $builder->add(
            'current_password',
            PasswordType::class,
            array_merge($options, [
                // Use full domain as field is not mapped.
                AbstractForm::FIELD_OPTION_NAME_LABEL => EntityHelper::getEntityPropertyTrans(
                    User::class,
                    'passwordCurrent'
                ),
                AbstractForm::FIELD_OPTION_NAME_MAPPED => false,
                AbstractForm::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_LOCK,
                AbstractForm::FIELD_OPTION_NAME_SHOW_BUTTON => true,
                AbstractForm::FIELD_OPTION_NAME_CONSTRAINTS => [
                    new NotBlank(),
                    new UserPassword($constraintsOptions),
                ],
                AbstractForm::FIELD_OPTION_NAME_ATTR => [
                    'autocomplete' => 'current-password',
                ],
            ])
        );

        return $this;
    }

    public function builderAddPasswordAndConfirmation(
        FormBuilderInterface $builder
    ) {
        $builder
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'options' => [
                        'attr' => [
                            'autocomplete' => 'new-password',
                        ],
                    ],
                    'first_options' => [
                        AbstractForm::FIELD_OPTION_NAME_LABEL => 'password',
                        AbstractForm::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_LOCK,
                        AbstractForm::FIELD_OPTION_NAME_SHOW_BUTTON => true,
                    ],
                    'second_options' => [
                        AbstractForm::FIELD_OPTION_NAME_LABEL => 'password_confirmation',
                        AbstractForm::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_REPEAT,
                        AbstractForm::FIELD_OPTION_NAME_SHOW_BUTTON => true,
                    ],
                    'invalid_message' => 'fos_user.password.mismatch',
                ]
            );

        return $this;
    }
}
