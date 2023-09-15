<?php

namespace Wexample\SymfonyHelpers\Tests\Traits;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;

trait DoctrineTestCase
{
    public function getEntityManager(): EntityManager
    {
        // Allow overriding used entity manager.
        return $this->getDoctrine()->getManager();
    }

    public function getDoctrine(): Registry
    {
        return self::getContainer()->get('doctrine');
    }

    public function entityRefresh(AbstractEntityInterface $entity): ?AbstractEntityInterface
    {
        return $this->getRepository($entity::class)->find($entity->getId());
    }

    /**
     * We should get repository from app container
     * to not have detached entities issues.
     */
    public function getRepository(string $className): EntityRepository
    {
        return $this->getEntityManager()->getRepository($className);
    }
}
