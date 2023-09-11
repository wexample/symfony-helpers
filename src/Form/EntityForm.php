<?php

namespace Wexample\SymfonyHelpers\Form;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Wexample\SymfonyHelpers\Form\AbstractForm;

class EntityForm extends AbstractForm
{
    public function __construct(
        protected ParameterBagInterface $parameterBag
    ) {
    }
}
