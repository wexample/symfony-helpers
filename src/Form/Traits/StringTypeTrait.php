<?php

namespace Wexample\SymfonyHelpers\Form\Traits;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Length;
use Wexample\SymfonyHelpers\Form\AbstractForm;
use Wexample\SymfonyHelpers\Form\Traits\MaterializeFieldTypeTrait;
use function is_object;

trait StringTypeTrait
{
    use MaterializeFieldTypeTrait;

    /**
     * Add attribute "maxlength" on field, regarding "Length" annotation on
     * entity field.
     */
    protected function stringRestrictLength(FormView $view, FormInterface $form)
    {
        if (!isset($view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR]['maxlength'])) {
            $entity = $form->getRoot()->getData();

            if (!is_object($entity)) {
                return;
            }

            try {
                $reflexion = new ReflectionClass($entity);
                if ($reflexion->hasProperty($form->getName())) {
                    $refProp = $reflexion
                        ->getProperty(
                            $form->getName()
                        );
                    $annotationReader = new AnnotationReader();
                    $annotations = $annotationReader
                        ->getPropertyAnnotations(
                            $refProp
                        );

                    foreach ($annotations as $annotation) {
                        if ($annotation instanceof Length) {
                            $view->vars[AbstractForm::FIELD_OPTION_NAME_ATTR]['maxlength'] = $annotation->max;
                        }
                    }
                }
            } catch (ReflectionException) {
            }
        }
    }
}
