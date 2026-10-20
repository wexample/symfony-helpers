<?php

namespace Wexample\SymfonyHelpers\Interface;

/**
 * A field of an entity read as the reader would see it rather than as stored:
 * in the visitor's language, for a field symfony-translations translates.
 * Whoever describes an entity for the outside — its schema.org data — reads
 * through it, so that nothing it writes has to know translations exist.
 */
interface EntityFieldReaderInterface
{
    public const string TAG = 'wexample.entity_field_reader';

    /**
     * Whether this reader answers for the field; the property as stored
     * otherwise.
     */
    public function supportsField(object $entity, string $field): bool;

    /**
     * @param string|null $locale The current locale if omitted
     */
    public function readField(object $entity, string $field, ?string $locale = null): mixed;
}
