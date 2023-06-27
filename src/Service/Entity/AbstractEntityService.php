<?php

namespace Wexample\SymfonyHelpers\Service\Entity;

use Exception;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;
use Wexample\SymfonyHelpers\Traits\EntityManipulatorTrait;
use function call_user_func_array;

abstract class AbstractEntityService extends EntityNeutralService
{
    use EntityManipulatorTrait;

    /**
     * @throws Exception
     */
    public function __call(
        $method,
        $arguments
    ): mixed {
        if (str_starts_with($method, 'create')) {
            $className = substr($method, 6);

            if (ClassHelper::getShortName($this->getEntityClassName()) === $className) {
                $entity = $this->createEntity();

                array_unshift($arguments, $entity);

                $fillMethodName = 'fill'.$className;

                if (method_exists($this, $fillMethodName) && is_callable(array($this, $fillMethodName))) {
                    return call_user_func_array([$this, $fillMethodName], $arguments);
                } else {
                    throw new Exception('Unable to find method '.$fillMethodName.'() on '.$this::class);
                }
            }
        }

        return null;
    }

    public function createEntity(): AbstractEntityInterface
    {
        $className = $this->getEntityClassName();

        return new $className();
    }

    public function getEntityRepository(): AbstractRepository
    {
        /** @var AbstractRepository $repo */
        $repo = $this->getEntityManager()->getRepository(
            $this->getEntityClassName()
        );

        return $repo;
    }
}