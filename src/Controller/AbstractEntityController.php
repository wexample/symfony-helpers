<?php

namespace Wexample\SymfonyHelpers\Controller;

use Wexample\SymfonyHelpers\Controller\Traits\EntityControllerTrait;

/**
 * Reference trait and build simple constructor.
 * Use this class as shorthand if you have only one parent inherited class.
 */
abstract class AbstractEntityController extends AbstractController
{
    use EntityControllerTrait;
}
