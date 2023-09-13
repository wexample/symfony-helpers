<?php

namespace Wexample\SymfonyHelpers\Form;

use App\Wex\BaseBundle\Helper\DomHelper;

abstract class AbstractType extends \Symfony\Component\Form\AbstractType
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

    protected function resolveTypeClass(string $className): string
    {
        $map = $this->getFormTypeClassesMap();
        return $map[$className] ?? $className;
    }

    protected function getFormTypeClassesMap(): array
    {
        return [];
    }
}