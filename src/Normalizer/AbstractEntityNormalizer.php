<?php

namespace Wexample\SymfonyHelpers\Normalizer;

use ArrayObject;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;
use Wexample\SymfonyHelpers\Helper\DateHelper;
use Wexample\SymfonyHelpers\Helper\EntityHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use Wexample\SymfonyHelpers\Interface\NormalizableDataInterface;

abstract class AbstractEntityNormalizer extends AbstractNormalizer implements NormalizerAwareInterface
{
    use EntityManipulatorTrait;
    use NormalizerAwareTrait;

    public bool $isEntrypoint = true;

    /**
     * @param AbstractEntity $data
     * @param string|null $format
     * @param array $context
     * @return array|string|int|float|bool|ArrayObject|null
     */
    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null {
        $entity = $this->normalizeEntity(
            entity: $data,
            format: $format,
            context: $context
        );

        if ($entity instanceof NormalizableDataInterface) {
            $entity = $entity->toArray();
        }

        $relationships = $this->normalizeRelationships(
            entity: $data,
            format: $format,
            context: $context
        );

        $metadata = $this->normalizeMetadata(
            entity: $data,
            format: $format,
            context: $context
        );

        $autoRelationships = [];

        if (is_array($entity)) {
            $autoRelationships = $this->collectRelationshipsFromEntityData(
                $entity,
                $format,
                $context
            );
        }

        if (is_array($metadata)) {
            $autoRelationships = $autoRelationships + $this->collectRelationshipsFromEntityData(
                $metadata,
                $format,
                $context
            );
        }

        $relationships = $relationships + $autoRelationships;

        return [
            'type' => ClassHelper::getFieldName($data),
            'entity' => $entity,
            'metadata' => $metadata,
            'relationships' => $relationships,
        ];
    }

    protected function normalizeRelationships(
        AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null {
        return [];
    }

    protected function normalizeMetadata(
        AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null {
        return [];
    }

    protected function normalizeEntity(
        AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|NormalizableDataInterface|null {
        return [
            $this->buildIdKey() => $this->buildIdValue($entity, $context),
        ];
    }

    protected function shouldAutoRelationships(array $context = []): bool
    {
        return ($context['auto_relationships'] ?? true) === true;
    }

    /**
     * Replaces every related entity found in $entityData by its id.
     *
     * The related items themselves are collected only for the entrypoint item:
     * nested items reference their own relations by id only, so a single
     * response never cascades through the whole entity graph.
     */
    protected function collectRelationshipsFromEntityData(
        array &$entityData,
        ?string $format = null,
        array $context = []
    ): array {
        $relationships = [];

        foreach ($entityData as $key => $value) {
            $entityData[$key] = $this->extractRelationshipsFromValue(
                $value,
                $relationships,
                $format,
                $context
            );
        }

        return $relationships;
    }

    protected function extractRelationshipsFromValue(
        mixed $value,
        array &$relationships,
        ?string $format = null,
        array $context = []
    ): mixed {
        if ($value instanceof AbstractEntity) {
            $id = (string) $value->getId();

            if ($this->shouldAutoRelationships($context) && isset($this->normalizer)) {
                $nestedContext = $context;
                $nestedContext['auto_relationships'] = false;

                $relationships[$id] = $this->normalizer->normalize(
                    $value,
                    $format,
                    $nestedContext
                );
            }

            return $id;
        }

        if ($value instanceof Collection) {
            $normalized = [];
            foreach ($value as $item) {
                $normalized[] = $this->extractRelationshipsFromValue(
                    $item,
                    $relationships,
                    $format,
                    $context
                );
            }

            return $normalized;
        }

        if (is_array($value)) {
            foreach ($value as $index => $item) {
                $value[$index] = $this->extractRelationshipsFromValue(
                    $item,
                    $relationships,
                    $format,
                    $context
                );
            }

            return $value;
        }

        return $value;
    }

    protected function buildIdKey(): string
    {
        return VariableHelper::ID;
    }

    protected function buildIdValue(
        AbstractEntity $object,
        array $context = []
    ): string {
        return (string) $object->getId();
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            static::getEntityClassName() => true,
        ];
    }

    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = []
    ): bool {
        return $this->isEntrypoint && ClassHelper::isClassPath($data, static::getEntityClassName());
    }

    protected function normalizeDateTimeOrNull(
        \DateTimeInterface|null $dateTime,
        array $context = []
    ): ?string {
        return $dateTime ? $this->normalizeDateTime($dateTime, $context) : null;
    }

    protected function normalizeDateTime(
        \DateTimeInterface $dateTime,
        array $context = []
    ): string {
        if ($dateTimeFormat = $this->getDefaultDateTimeSerializationFormat()) {
            $context[DateTimeNormalizer::FORMAT_KEY] = $dateTimeFormat;
        }

        return (new DateTimeNormalizer($context))->normalize($dateTime);
    }

    protected function getDefaultDateTimeSerializationFormat(): ?string
    {
        // Use this popular format for apis.
        return DateHelper::DATE_PATTERN_TIME_ZULU;
    }

    /**
     * @param array<AbstractEntity>|Collection<AbstractEntity> $entities
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalizeEntitiesCollection(
        array|Collection $entities,
        ?string $format = null,
        array $context = []
    ): array {
        return $this->normalizeCollection(
            // Sort entities to maintain a constant order across exports.
            EntityHelper::sortById($entities),
            $format,
            $context
        );
    }
}
