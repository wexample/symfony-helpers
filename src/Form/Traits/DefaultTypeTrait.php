<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Form\AbstractForm;
use Wexample\SymfonyHelpers\Helper\EntityHelper;
use Wexample\SymfonyTranslations\Translation\Translator;
use function str_contains;

trait DefaultTypeTrait
{
    public function materializeConfigureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                AbstractForm::FIELD_OPTION_NAME_LABEL => true,
            ]
        );
    }

    protected function defaultTypeBuildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        $rootData = $form->getRoot()->getData();

        // The form data is mapped to an entity.
        if (isset($options[AbstractForm::FIELD_OPTION_NAME_MAPPED]) &&
            $options[AbstractForm::FIELD_OPTION_NAME_MAPPED] &&
            $rootData instanceof AbstractEntityInterface) {
            if ($label = $view->vars[AbstractForm::FIELD_OPTION_NAME_LABEL]) {
                // Find it from field name as fallback.
                if (true === $label) {
                    $label = $form->getName();
                }

                // The label has no specified translation domain.
                if (!str_contains((string) $label, Translator::DOMAIN_SEPARATOR)) {
                    // Build full length translation id.
                    $view->vars['label'] = EntityHelper::getEntityPropertyTrans(
                        $rootData,
                        $label
                    );
                } else {
                    $view->vars['label'] = $label;
                }
            }
        } else {
            $transKeyBase = $this->getTransKeyBase($view, $form);

            if (true === $view->vars['label']) {
                $view->vars['label'] = $transKeyBase.'.label';
            }
        }

        if (isset($view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR][AbstractForm::FIELD_OPTION_VALUE_ATTR_PLACEHOLDER]) &&
            true === $view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR][AbstractForm::FIELD_OPTION_VALUE_ATTR_PLACEHOLDER]) {
            $transKeyBase = $this->getTransKeyBase($view, $form);

            $view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR][AbstractForm::FIELD_OPTION_VALUE_ATTR_PLACEHOLDER] = $transKeyBase.'.placeholder';
        }
    }

    protected function getTransKeyBase(
        FormView $view,
        FormInterface $form
    ): string {
        return AbstractForm::transForm(
            'field.'.$view->vars['name'],
            $form
        );
    }
}
