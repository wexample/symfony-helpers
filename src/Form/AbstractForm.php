<?php

namespace Wexample\SymfonyHelpers\Form;

use App\Entity\User;
use App\Wex\BaseBundle\Helper\ClassHelper;
use App\Wex\BaseBundle\Helper\DomHelper;
use App\Wex\BaseBundle\Helper\VariableHelper;
use App\Wex\BaseBundle\Service\FormProcessor\AbstractFormProcessor;
use App\Wex\BaseBundle\Translation\Translator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\NotBlank;
use Wexample\SymfonyHelpers\Helper\IconMaterialHelper;

class AbstractForm extends AbstractType
{
    public const FIELD_OPTION_NAME_ATTR = 'attr';

    public const FIELD_OPTION_NAME_ALLOW_EXTRA_FIELDS = 'allow_extra_fields';

    public const FIELD_OPTION_NAME_CHARACTER_COUNTER = 'character_counter';

    public const FIELD_OPTION_NAME_CHOICES = 'choices';

    public const FIELD_OPTION_NAME_CLASS = DomHelper::ATTRIBUTE_CLASS;

    public const FIELD_OPTION_NAME_COLOR = 'color';

    public const FIELD_OPTION_NAME_DISABLED = 'disabled';

    public const FIELD_OPTION_NAME_SHOW_BUTTON = 'show_button';

    public const FIELD_OPTION_NAME_CONSTRAINTS = 'constraints';

    public const FIELD_OPTION_NAME_DATA = 'data';

    public const FIELD_OPTION_NAME_DATA_CLASS = 'data_class';

    public const FIELD_OPTION_NAME_DATE_TYPE = 'date_type';

    public const FIELD_OPTION_NAME_DATE_PICKER = 'date_picker';

    public const FIELD_OPTION_NAME_DRAG_ZONE = 'drag_zone';

    public const FIELD_OPTION_NAME_EMPTY_DATA = 'empty_data';

    public const FIELD_OPTION_NAME_FILLED = 'filled';

    public const FIELD_OPTION_NAME_HELPER = 'helper';

    public const FIELD_OPTION_NAME_HELPER_PARAMS = 'helper_params';

    public const FIELD_OPTION_NAME_ICON = 'icon';

    public const FIELD_OPTION_NAME_IN_FOOTER = 'in_footer';

    public const FIELD_OPTION_NAME_LABEL = 'label';

    public const FIELD_OPTION_NAME_LABEL_PARAMS = 'label_params';

    public const FIELD_OPTION_NAME_MAPPED = 'mapped';

    public const FIELD_OPTION_NAME_MULTIPLE = 'multiple';

    public const FIELD_OPTION_NAME_REQUIRED = 'required';

    public const FIELD_OPTION_NAME_SANITIZE = 'sanitize';

    public const FIELD_OPTION_NAME_VALIDATE = 'validate';

    public const FIELD_OPTION_NAME_VUE_WIDGET = 'vue_widget';

    public const FIELD_OPTION_NAME_YEARS = 'years';

    public const FIELD_OPTION_VALUE_ATTR_PLACEHOLDER = 'placeholder';

    public static bool $ajax = false;

    public bool $title = true;

    public string $translationDomain;

    public static function getFormProcessorClass(): string
    {
        return ClassHelper::getCousin(
            static::class,
            AbstractFormProcessor::FORMS_CLASS_BASE_PATH,
            '',
            AbstractFormProcessor::FORMS_PROCESSOR_CLASS_BASE_PATH,
            AbstractFormProcessor::CLASS_EXTENSION
        );
    }

    public static function transForm(
        string $key,
        FormInterface $form
    ): string {
        return self::transFormDomain($form)
            .Translator::DOMAIN_SEPARATOR
            .$key;
    }

    public static function transFormDomain(
        FormInterface $form
    ): string {
        return self::transTypeDomain(
            $form
                ->getRoot()
                ->getConfig()
                ->getType()
                ->getInnerType()
        );
    }

    public static function transTypeDomain(
        $type
    ): string {
        // Build form name.
        return 'forms.'.ClassHelper::longTableized($type, '.');
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        $view->ajax = $this::$ajax;
        $view->vars['title'] = $this->title ? $this->trans(
            'form_title'
        ) : $this->title;

        if ($this::$ajax) {
            $view->vars[self::FIELD_OPTION_NAME_ATTR]['class'] = 'form-ajax';
        }
    }

    /**
     * Create translatable string with form domain prefix.
     */
    public function trans(string $key): string
    {
        return self::transTypeDomain($this).Translator::DOMAIN_SEPARATOR.$key;
    }

    public function builderAddTitle(
        FormBuilderInterface $builder,
        array $options = []
    ) {
        $builder
            ->add(
                'title',
                TextType::class,
                $this->resolveOptions([
                    self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_LABEL,
                    self::FIELD_OPTION_NAME_EMPTY_DATA => '', // Avoid exception on null value.
                ], $options)
            );
    }

    protected function resolveOptions(
        array $defaultOptions,
        array $extraOptions = []
    ) {
        $options = $extraOptions + $defaultOptions;

        if (isset($options[self::FIELD_OPTION_NAME_REQUIRED]) && $options[self::FIELD_OPTION_NAME_REQUIRED]) {
            $options[self::FIELD_OPTION_NAME_CONSTRAINTS][] = new NotBlank();
        }

        return $options;
    }

    public function builderAddSave(
        FormBuilderInterface $builder,
        array $options = [],
        string $child = VariableHelper::SAVE
    ): AbstractForm {
        $builder->add(
            $child,
            SubmitType::class,
            $this->resolveOptions(
                [
                    self::FIELD_OPTION_NAME_LABEL => 'label.'.$child,
                    self::FIELD_OPTION_NAME_IN_FOOTER => true,
                ],
                $options
            )
        );

        return $this;
    }

    public function builderAddUser(
        FormBuilderInterface $builder,
        array $options = []
    ) {
        $entity = $builder->getData();

        $builder->add(
            'user',
            EntitySearchType::class,
            $this->resolveOptions([
                self::FIELD_OPTION_NAME_CLASS => User::class,
                self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_PERSON,
                // Don't know how to do this internally in form type :
                // getting mapped entity data.
                // TODO Support entity having no getUser properties, but it should not.
                //      See issue https://gitlab.wexample.com/wexample/network/-/issues/241
                self::FIELD_OPTION_NAME_DATA => method_exists($entity, 'getUser') ? $entity->getUser() : null,
            ], $options)
        );

        return $this;
    }

    public function builderAddStatus(
        FormBuilderInterface $builder,
        array $values,
        array $options = []
    ) {
        $builder
            ->add(
                VariableHelper::STATUS,
                // Use text type for materialize field.
                ChoiceType::class,
                $this->resolveOptions([
                    self::FIELD_OPTION_NAME_CHOICES => $values,
                    self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_LOCAL_OFFER,
                ], $options)
            );
    }

    protected function builderAddUrl(
        FormBuilderInterface $builder,
        array $options = []
    ) {
        $options = $this->resolveOptions(
            [
                self::FIELD_OPTION_NAME_LABEL => true,
                self::FIELD_OPTION_NAME_ATTR => [
                    self::FIELD_OPTION_VALUE_ATTR_PLACEHOLDER => true,
                ],
                self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_PUBLIC,
                self::FIELD_OPTION_NAME_HELPER => true,
                self::FIELD_OPTION_NAME_REQUIRED => false,
            ],
            $options
        );

        $builder->add(
            'url',
            UrlType::class,
            $options
        );
    }

    protected function buildDateOptions(array $options = []): array
    {
        return $options + [
                self::FIELD_OPTION_NAME_DATE_PICKER => 'html5',
                self::FIELD_OPTION_NAME_ICON => IconMaterialHelper::ICON_TODAY,
            ];
    }

    public function builderAddSubmit(
        FormBuilderInterface $builder,
    ) {
        $builder->add(
            'submit',
            SubmitType::class,
            [
                self::FIELD_OPTION_NAME_LABEL => 'label.step.next',
                self::FIELD_OPTION_NAME_IN_FOOTER => true,
            ]
        );
    }
}
