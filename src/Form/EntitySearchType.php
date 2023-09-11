<?php

namespace Wexample\SymfonyHelpers\Form;

use App\Wex\BaseBundle\Api\Dto\Traits\EntityDtoTrait;
use App\Wex\BaseBundle\Entity\AbstractEntity;
use App\Wex\BaseBundle\Helper\ClassHelper;
use App\Wex\BaseBundle\Helper\DomHelper;
use App\Wex\BaseBundle\Helper\EntityHelper;
use App\Wex\BaseBundle\Helper\TextHelper;
use App\Wex\BaseBundle\Helper\VariableHelper;
use App\Wex\BaseBundle\Repository\AbstractRepository;
use App\Wex\BaseBundle\Service\Search;
use App\Wex\BaseBundle\Twig\AssetsExtension;
use App\Wex\BaseBundle\Twig\TranslationExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use function count;
use function current;
use function explode;
use function implode;
use function is_array;

class EntitySearchType extends TextType
{
    /**
     * @var string
     */
    private const VAR_LABEL_ATTR = 'label_attr';

    final public const FIELD_OPTION_DELETE_ORPHAN_ENTITIES = 'delete_orphan_entities';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AssetsExtension $assetsExtension,
        private readonly TranslationExtension $transExtension
    ) {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            DomHelper::ATTRIBUTE_CLASS,
            AbstractForm::FIELD_OPTION_NAME_MULTIPLE,
        ]);

        $resolver->setDefault(AbstractForm::FIELD_OPTION_NAME_DATA, null);
        $resolver->setDefault(AbstractForm::FIELD_OPTION_NAME_DATA_CLASS, null);
        $resolver->setDefault(AbstractForm::FIELD_OPTION_NAME_MULTIPLE, false);
        $resolver->setDefault(AbstractForm::FIELD_OPTION_NAME_VUE_WIDGET, null);
        $resolver->setDefault(self::FIELD_OPTION_DELETE_ORPHAN_ENTITIES, false);

        parent::configureOptions($resolver);
    }

    /**
     * @throws ExceptionInterface
     */
    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        parent::buildView($view, $form, $options);

        // Allow single value or array.
        $data = $options[AbstractForm::FIELD_OPTION_NAME_MULTIPLE]
            ? $options[AbstractForm::FIELD_OPTION_NAME_DATA]
            : [$options[AbstractForm::FIELD_OPTION_NAME_DATA]];
        $data = !is_null($data) ? $data : [];

        $results = [];
        foreach ($data as $entity) {
            // Avoid null values.
            if ($entity) {
                $result =
                    Search::createEntitySearchResult($entity);
                $result->entity =
                    $this->assetsExtension->serializeEntity($result->entity);
                $result->entity['displayFormat'] =
                    EntityDtoTrait::DISPLAY_FORMAT_SMALL;
                $results[] =
                    $result;
            }
        }

        $view->vars['results'] = $results;
        $view->vars['max_autocomplete'] = $options['max_results'] ?? 10;
        $view->vars['max_values'] = $options['max_values'] ?? ($options[AbstractForm::FIELD_OPTION_NAME_MULTIPLE] ? 10 : 1);
        $view->vars['search_action'] = $view->vars[VariableHelper::ID];

        $view->vars['vue_widget'] =
            $options['vue_widget'];
        $view->vars['vue_multiple'] =
            TextHelper::renderBoolean($options[AbstractForm::FIELD_OPTION_NAME_MULTIPLE]);
        $view->vars['vue_required'] =
            TextHelper::renderBoolean($options[AbstractForm::FIELD_OPTION_NAME_REQUIRED]);

        $view->vars[self::VAR_LABEL_ATTR][DomHelper::ATTRIBUTE_CLASS] = 'active';

        // Applied on the hidden field.
        $view->vars[AbstractForm::FIELD_OPTION_NAME_REQUIRED] = $options[AbstractForm::FIELD_OPTION_NAME_REQUIRED];
        $view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR][DomHelper::ATTRIBUTE_CLASS] = false;

        // Disable the "required" field as it is managed by vue.
        $view->vars[self::VAR_LABEL_ATTR]['required_display'] = 'false';

        // The label is rendered by vue, so content should be dynamic.
        if ($view->vars['label']) {
            $view->vars[self::VAR_LABEL_ATTR]['v-html'] = 'trans(label)';
            $this->transExtension->transJs($view->vars['label']);
        }

        // Renders errors for vue;
        $errorsRendered = [];
        /** @var FormError $error */
        foreach ($view->vars['errors'] as $error) {
            $errorsRendered[] = $error->getMessage();
        }

        $view->vars['errors_rendered'] = $errorsRendered;
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $repos = [];
        $classesTypes = is_array($options[DomHelper::ATTRIBUTE_CLASS])
            ? $options[DomHelper::ATTRIBUTE_CLASS]
            : [$options[DomHelper::ATTRIBUTE_CLASS]];

        foreach ($classesTypes as $classesType) {
            $shortName = ClassHelper::getTableizedName(
                $classesType
            );
            $repos[$shortName] = $this->em->getRepository($classesType);
        }

        $builder
            ->addModelTransformer(
                new CallbackTransformer(
                    function() use
                    (
                        $options
                    ): ?string {
                        $value = [];

                        if (isset($options[AbstractForm::FIELD_OPTION_NAME_DATA])) {
                            // Allow single value or array.
                            $data = $options[AbstractForm::FIELD_OPTION_NAME_MULTIPLE] ? $options[AbstractForm::FIELD_OPTION_NAME_DATA] : [$options[AbstractForm::FIELD_OPTION_NAME_DATA]];
                            $data = !is_null($data) ? $data : [];

                            /** @var AbstractEntity $entity */
                            foreach ($data as $entity) {
                                $value[] = EntityHelper::createEntityId(
                                    $entity
                                );
                            }
                        }

                        $options['entities'] = $options[AbstractForm::FIELD_OPTION_NAME_DATA];

                        // transform the array to a string
                        return empty($value) ? null : implode(',', $value);
                    },
                    function(
                        $tagsAsString
                    ) use
                    (
                        $options,
                        $repos
                    ) {
                        $entities = [];
                        $deleteOrphans = $options[self::FIELD_OPTION_DELETE_ORPHAN_ENTITIES];
                        $previousEntitiesRegistry = [];

                        if ($deleteOrphans) {
                            /** @var AbstractEntity $entity */
                            foreach ($options[AbstractForm::FIELD_OPTION_NAME_DATA] as $entity) {
                                $previousEntitiesRegistry[$entity->getId()] = $entity;
                            }
                        }

                        $selections = $tagsAsString ? explode(',', $tagsAsString) : [];
                        foreach ($selections as $value) {
                            $exp = explode('-', $value);

                            if (2 === count($exp) && isset($repos[$exp[1]])) {
                                /** @var AbstractRepository $repo */
                                $repo = $repos[$exp[1]];
                                $id = (int) $exp[0];
                                if ($id) {
                                    $entities[] = $repo->find($id);

                                    if (isset($previousEntitiesRegistry[$id])) {
                                        unset($previousEntitiesRegistry[$id]);
                                    }
                                }
                            } // Keep value if not valid, may be used later.
                            else {
                                $entities[] = $value;
                            }
                        }

                        if ($deleteOrphans) {
                            /** @var AbstractEntity $entity */
                            foreach ($previousEntitiesRegistry as $entity) {
                                $this->em->remove($entity);
                            }

                            $this->em->flush();
                        }

                        if (empty($entities)) {
                            return null;
                        }

                        if (!$options[AbstractForm::FIELD_OPTION_NAME_MULTIPLE]) {
                            return current($entities);
                        }

                        return $entities;
                    }
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'entity_search';
    }
}
