<?php

namespace Wexample\SymfonyHelpers\Form;

use App\Wex\BaseBundle\Helper\VariableHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wexample\SymfonyHelpers\Form\FileType;

class PictureType extends FileType
{
    /**
     * @var string
     */
    private const VAR_PREVIEW_ROUTE = 'preview_route';

    /**
     * @var string
     */
    private const VAR_PREVIEW_OPTIONS = 'preview_options';

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return VariableHelper::PICTURE;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                self::VAR_PREVIEW_ROUTE => false,
                self::VAR_PREVIEW_OPTIONS => false,
            ]
        );

        $this->materializeConfigureOptions($resolver);
        parent::configureOptions($resolver);
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options
    ) {
        $view->vars[self::VAR_PREVIEW_ROUTE] = $options[self::VAR_PREVIEW_ROUTE] ?? null;
        $view->vars[self::VAR_PREVIEW_OPTIONS] = $options[self::VAR_PREVIEW_OPTIONS] ?? null;

        parent::buildView($view, $form, $options);
    }
}
