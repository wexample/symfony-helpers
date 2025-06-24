<?php

namespace Wexample\SymfonyHelpers\Normalizer;

use ArrayObject;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;
use Wexample\SymfonyHelpers\Helper\DateHelper;
use Wexample\SymfonyHelpers\Helper\EntityHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

abstract class AbstractEntityNormalizer extends AbstractNormalizer
{
    use EntityManipulatorTrait;

    public bool $isEntrypoint = True;

    /**
     * @param AbstractEntity $object
     * @param string|null $format
     * @param array $context
     * @return array|string|int|float|bool|ArrayObject|null
     */
    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|null|ArrayObject
    {
        return [
            $this->buildIdKey() => $this->buildIdValue($object, $context),
        ];
    }

    protected function buildIdKey(): string
    {
        return VariableHelper::ID;
    }

    protected function buildIdValue(
        AbstractEntity $object,
        array $context = []
    ): string|int
    {
        return $object->getId();
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
    ): bool
    {
        return $this->isEntrypoint && ClassHelper::isClassPath($data, static::getEntityClassName());
    }

    protected function normalizeDateTimeOrNull(
        \DateTimeInterface|null $dateTime,
        array $context = []
    ): ?string
    {
        return $dateTime ? $this->normalizeDateTime($dateTime, $context) : null;
    }

    protected function normalizeDateTime(
        \DateTimeInterface $dateTime,
        array $context = []
    ): string
    {
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
    ): array
    {
        return $this->normalizeCollection(
        // Sort entities to maintain a constant order across exports.
            EntityHelper::sortById($entities),
            $format,
            $context
        );
    }
}