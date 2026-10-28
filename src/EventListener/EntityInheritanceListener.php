<?php

namespace Wexample\SymfonyHelpers\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Wexample\SymfonyHelpers\Helper\EntityHierarchyHelper;

/**
 * Gives a hierarchy of entities the discriminator values the stack names them
 * by, in place of Doctrine's default.
 *
 * A root cannot list its subclasses when they live in other packages — the
 * design system's session knows nothing of the one the Syrtis client adds —,
 * so Doctrine builds the map itself, from every subclass it finds, under the
 * lowercased class names. The map is rebuilt here over the same classes with
 * the names EntityHierarchyHelper gives them, so that the column stores the
 * word the api serves and the browser reads. A root that does write its own
 * map is left with it.
 */
#[AsDoctrineListener(event: Events::loadClassMetadata)]
class EntityInheritanceListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $metadata = $args->getClassMetadata();

        if (! $metadata->isRootEntity()
            || $metadata->isInheritanceTypeNone()
            || $metadata->getReflectionClass()->getAttributes(DiscriminatorMap::class)) {
            return;
        }

        $map = EntityHierarchyHelper::buildDiscriminatorMap($metadata->discriminatorMap);

        $metadata->discriminatorMap = [];
        $metadata->discriminatorValue = null;
        $metadata->setDiscriminatorMap($map);
    }
}
