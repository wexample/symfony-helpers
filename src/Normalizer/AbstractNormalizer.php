<?php

namespace Wexample\SymfonyHelpers\Normalizer;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractNormalizer implements NormalizerInterface
{
    public function normalizeCollection(
        array|Collection $items,
        ?string $format = null,
        array $context = []
    ): array
    {
        $output = [];

        foreach ($items as $item) {
            $output[] = $this->normalize($item, $format, $context);
        }

        return $output;
    }
}