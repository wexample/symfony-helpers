<?php

namespace Wexample\SymfonyHelpers\Service\Entity;

use Wexample\SymfonyHelpers\Entity\SystemParameter;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\SystemParameterEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Repository\SystemParameterRepository;

abstract class SystemParameterEntityService extends AbstractEntityService
{
    use SystemParameterEntityManipulatorTrait;

    public function addOrGetValue(
        string $name,
        string|int|null $default
    ): string|int|null {
        return $this->addOrGet(
            $name,
            $default
        )->getValue();
    }

    public function addOrGet(
        string $name,
        string|int|null $default
    ): SystemParameter {
        /** @var SystemParameterRepository $repo */
        $repo = $this->getEntityRepository();

        // Using count supports database value to be null.
        if ($repo->countByName($name)) {
            return $repo->findOneByName($name);
        }

        $parameter = $this->createSystemParameter(
            $name,
            $default
        );

        $repo->add($parameter);

        return $parameter;
    }

    public function fillSystemParameter(
        SystemParameter $systemParameter,
        string $name,
        string|int|null $value
    ): SystemParameter {
        $systemParameter->setName($name);
        $systemParameter->setValue($value);

        return $systemParameter;
    }
}
