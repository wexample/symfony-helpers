<?php

namespace Wexample\SymfonyHelpers\Normalizer;

use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Wexample\Helpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;
use Wexample\SymfonyHelpers\Helper\DateHelper;

abstract class AbstractEntityNormalizer extends AbstractNormalizer
{
    use EntityManipulatorTrait;

    public bool $isEntrypoint = True;

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
}