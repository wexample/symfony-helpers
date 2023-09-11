<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use App\Wex\BaseBundle\Helper\VariableHelper;
use App\Wex\BaseBundle\Translation\Translator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\AbstractForm;
use function implode;
use function strpos;

trait MaterializeFieldTypeTrait
{
    use DefaultTypeTrait;

    public static string $ICON_ACCOUNT_BALANCE = 'account_balance';

    public static string $ICON_ACCOUNT_BOX = 'account_box';

    public static string $ICON_ASSIGNMENT_TURNED_ID = 'assignment_turned_in';

    public static string $ICON_ATTACH_MONEY = 'attach_money';

    public static string $ICON_BUSINESS = 'business';

    public static string $ICON_CALL_SPLIT = 'call_split';

    public static string $ICON_CHAT = 'chat';

    public static string $ICON_CHAT_BUBBLE = 'chat_bubble';

    public static string $ICON_CONTACT_PHONE = 'contact_phone';

    public static string $ICON_CONTENT_COPY = 'content_copy';

    public static string $ICON_EMAIL = VariableHelper::EMAIL;

    public static string $ICON_ENVELOPE = 'envelope';

    public static string $ICON_EVENT = 'event';

    public static string $ICON_EVENT_AVAILABLE = 'event_available';

    public static string $ICON_FACE = 'face';

    public static string $ICON_FILTER_1 = 'filter_1';

    public static string $ICON_FILTER_2 = 'filter_2';

    public static string $ICON_FILTER_3 = 'filter_3';

    public static string $ICON_FINGERPRINT = 'fingerprint';

    public static string $ICON_FORMAT_LIST_NUMBERED = 'format_list_numbered';

    public static string $ICON_IMAGE = 'image';

    public static string $ICON_INSERT_DRIVE_FILE = 'insert_drive_file';

    public static string $ICON_LABEL = 'label';

    public static string $ICON_LINEAR_SCALE = 'linear_scale';

    public static string $ICON_LIST = 'list';

    public static string $ICON_LOCAL_OFFER = 'local_offer';

    public static string $ICON_LOCK = 'lock';

    public static string $ICON_MAIL = 'mail';

    public static string $ICON_MAP = 'map';

    public static string $ICON_MODE_EDIT = 'mode_edit';

    public static string $ICON_PERM_IDENTITY = 'perm_identity';

    public static string $ICON_PERSON = 'person';

    public static string $ICON_PHONE = 'phone';

    public static string $ICON_PLACE = 'place';

    public static string $ICON_PUBLIC = 'public';

    public static string $ICON_REPEAT = 'repeat';

    public static string $ICON_SHUFFLE = 'shuffle';

    public static string $ICON_STORE = 'store';

    public static string $ICON_SAVE = 'save';

    public static string $ICON_SEND = 'send';

    public static string $ICON_TAG = 'tag';

    public static string $ICON_TAGS = 'tags';

    public static string $ICON_TEXTSMS = 'textsms';

    public static string $ICON_TODAY = 'today';

    public static string $ICON_TRENDING_FLAT = 'trending_flat';

    public static string $ICON_USER_TAG = 'user-tag';

    public static string $ICON_USER = 'user';

    public function materializeConfigureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                AbstractForm::FIELD_OPTION_NAME_COLOR => null,
                AbstractForm::FIELD_OPTION_NAME_HELPER => null,
                AbstractForm::FIELD_OPTION_NAME_HELPER_PARAMS => null,
                AbstractForm::FIELD_OPTION_NAME_ICON => null,
                AbstractForm::FIELD_OPTION_NAME_IN_FOOTER => null,
                AbstractForm::FIELD_OPTION_NAME_LABEL_PARAMS => [],
                AbstractForm::FIELD_OPTION_NAME_VALIDATE => [],
            ]
        );
    }

    public function materializeBuildView(
        FormView $view,
        FormInterface $form,
        array $options,
        $classes = []
    ) {
        $this->defaultTypeBuildView($view, $form, $options);

        $view->vars[AbstractForm::FIELD_OPTION_NAME_COLOR] = $options[AbstractForm::FIELD_OPTION_NAME_COLOR] ?? '';
        $view->vars[AbstractForm::FIELD_OPTION_NAME_HELPER] = $options[AbstractForm::FIELD_OPTION_NAME_HELPER] ?? '';
        $view->vars[AbstractForm::FIELD_OPTION_NAME_HELPER_PARAMS] = $options[AbstractForm::FIELD_OPTION_NAME_HELPER_PARAMS] ?? '';
        $view->vars[AbstractForm::FIELD_OPTION_NAME_ICON] = $options[AbstractForm::FIELD_OPTION_NAME_ICON] ?? '';
        $view->vars[AbstractForm::FIELD_OPTION_NAME_IN_FOOTER] = $options[AbstractForm::FIELD_OPTION_NAME_IN_FOOTER] ?? '';
        $view->vars[AbstractForm::FIELD_OPTION_NAME_LABEL_PARAMS] = $options[AbstractForm::FIELD_OPTION_NAME_LABEL_PARAMS] ?? [];
        $view->vars[AbstractForm::FIELD_OPTION_NAME_VALIDATE] = $options[AbstractForm::FIELD_OPTION_NAME_VALIDATE];

        if (isset($options[AbstractForm::FIELD_OPTION_NAME_VALIDATE])) {
            $classes[] = AbstractForm::FIELD_OPTION_NAME_VALIDATE;

            // Support true for all available messages.
            if (true === $options[AbstractForm::FIELD_OPTION_NAME_VALIDATE]) {
                $view->vars[AbstractForm::FIELD_OPTION_NAME_VALIDATE] = ['success' => true, 'error' => true];
            }

            $transKeyBase = $this->getTransKeyBase($view, $form);

            $validate = $view->vars[AbstractForm::FIELD_OPTION_NAME_VALIDATE];
            if ($validate) {
                foreach ($validate as $key => $value) {
                    if (true === $value) {
                        $view->vars[AbstractForm::FIELD_OPTION_NAME_VALIDATE][$key] = $transKeyBase.'.validate.'.$key;
                    }
                }
            }
        }

        if (isset($view->vars['helper'])
            && true === $view->vars['helper']) {
            $view->vars['helper'] = $this->getTransKeyBase(
                    $view,
                    $form
                ).'.helper';
        } elseif (strpos($view->vars['helper'], Translator::DOMAIN_SEPARATOR)) {
            $view->vars['helper'] = AbstractForm::transForm(
                $view->vars['helper'],
                $form
            );
        }

        if (!empty($classes)) {
            if (!isset($view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR]['class'])) {
                $view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR]['class'] = '';
            }

            $view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR]['class'] .= ' '.implode(' ', $classes);
        }
    }
}
