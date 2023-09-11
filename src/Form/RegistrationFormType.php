<?php
/**
 * Created by PhpStorm.
 * User: weeger
 * Date: 02/02/19
 * Time: 15:23.
 */

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Wexample\SymfonyHelpers\Form\Traits\SecurityFormTrait;

class RegistrationFormType extends \FOS\UserBundle\Form\Type\RegistrationFormType
{
    use SecurityFormTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->builderAddPasswordAndConfirmation($builder);
    }
}
