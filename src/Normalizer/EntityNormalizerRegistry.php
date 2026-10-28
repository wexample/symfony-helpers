<?php

namespace Wexample\SymfonyHelpers\Normalizer;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Attribute\WorkerSafe;

/**
 * The entity normalizers of the app, and which one an entity goes to: the one
 * written for the nearest class in its ancestry.
 *
 * A normalizer written for a root answers for every subclass that has none of
 * its own; a subclass that has one takes it, whatever the normalizer the
 * caller holds — a listing of sessions normalized through the session
 * normalizer hands each Syrtis session to the Syrtis one. One answer per
 * class, so two normalizers can never both claim the same entity.
 */
class EntityNormalizerRegistry
{
    final public const string TAG = 'wexample.entity_normalizer';

    /** @var array<class-string, AbstractEntityNormalizer|null> */
    #[WorkerSafe('A cache of what the service definitions say, the same for every request.')]
    private array $byClass = [];

    /**
     * @param iterable<AbstractEntityNormalizer> $normalizers
     */
    public function __construct(
        #[AutowireIterator(self::TAG)]
        private readonly iterable $normalizers,
    ) {
    }

    public function findFor(object|string $entity): ?AbstractEntityNormalizer
    {
        $class = ClassHelper::getRealClassPath($entity);

        if (! array_key_exists($class, $this->byClass)) {
            $this->byClass[$class] = $this->resolve($class);
        }

        return $this->byClass[$class];
    }

    private function resolve(string $class): ?AbstractEntityNormalizer
    {
        $found = null;
        $foundClass = null;

        foreach ($this->normalizers as $normalizer) {
            $entityClass = $normalizer::getEntityClassName();

            if (! is_a($class, $entityClass, true)) {
                continue;
            }

            // The nearest ancestor wins: a normalizer for the class itself
            // over one for its parent, that one over one for the root.
            if (null === $foundClass || is_subclass_of($entityClass, $foundClass)) {
                $found = $normalizer;
                $foundClass = $entityClass;
            }
        }

        return $found;
    }
}
