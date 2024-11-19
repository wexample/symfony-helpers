<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasFormatTrait
{
    use HasLimitedValuesPropertyTrait;
    
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 120)]
    protected ?string $format = null;

    /**
     * Override this method to return an array of allowed formats.
     * @return array|null
     */
    public static function getAllowedFormats(): ?array
    {
        return null;
    }

    #[Pure]
    public function hasSomeFormat(array $formats): bool
    {
        return in_array(
            $this->getFormat(),
            $formats,
            true
        );
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     * @throws InvalidArgumentException If the format is not allowed.
     */
    public function setFormat(string $format): void
    {
        $this->checkAllowedOrFail(
            $format,
            $this->getAllowedFormats()
        );

        $this->format = $format;
    }

    #[Pure]
    public function isOfFormat(string $format): bool
    {
        return $this->getFormat() === $format;
    }
}
