<?php

namespace Wexample\SymfonyHelpers\Normalizer;

use ArrayObject;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasSecureIdTrait;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;
use Wexample\SymfonyHelpers\Helper\DateHelper;
use Wexample\SymfonyHelpers\Helper\EntityHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

abstract class AbstractEntityNormalizer extends AbstractNormalizer
{
    use EntityManipulatorTrait;

    public bool $isEntrypoint = true;

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null
    {
        return [
            'entity' => $this->normalizeEntity(
                entity: $data,
                format: $format,
                context: $context
            ),
            'metadata' => $this->normalizeMetadata(
                entity: $data,
                format: $format,
                context: $context
            ),
            'relationships' => $this->normalizeRelationships(
                entity: $data,
                format: $format,
                context: $context
            )
        ];
    }

    protected function normalizeRelationships(
        AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null
    {
        return [];
    }

    protected function normalizeMetadata(
        AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null
    {
        return [];
    }

    protected function normalizeEntity(
        AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null
    {
        return [
            $this->buildIdKey() => $this->buildIdValue($entity, $context),
        ];
    }

    protected function buildIdKey(): string
    {
        return 'secureId';
    }

    /**
     * @param AbstractEntity|HasSecureIdTrait $object
     * @param array $context
     * @return string|int
     */
    protected function buildIdValue(
        AbstractEntity $object,
        array $context = []
    ): string|int {
        return $object->getSecureId();
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
