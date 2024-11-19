<?php

namespace Wexample\SymfonyHelpers\Normalizer;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\DateHelper;

abstract class AbstractEntityNormalizer implements NormalizerInterface
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
    ): bool {
        return $this->isEntrypoint && ClassHelper::isClassPath($data, static::getEntityClassName());
    }

    public function normalizeCollection(
        array|Collection $items,
        ?string $format = null,
        array $context = []
    ): array {
        $output = [];

        foreach ($items as $item) {
            $output[] = $this->normalize($item, $format, $context);
        }

        return $output;
    }

    protected function serializeDateTime(
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
}