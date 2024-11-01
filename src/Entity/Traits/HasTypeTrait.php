<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasTypeTrait
{
    use HasLimitedValuesPropertyTrait;

    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 120)]
    protected ?string $type = null;

    /**
     * Override this method to return an array of allowed types.
     * @return array|null
     */
    public static function getAllowedTypes(): ?array
    {
        return null;
    }

    #[Pure]
    public function hasSomeType(array $types): bool
    {
        return in_array(
            $this->getType(),
            $types,
            true
        );
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @throws InvalidArgumentException If the type is not allowed.
     */
    public function setType(string $type): void
    {
        $this->checkAllowedOrFail(
            $type,
            $this->getAllowedTypes()
        );

        $this->type = $type;
    }

    #[Pure]
    public function isOfType(string $type): bool
    {
        return $this->getType() === $type;
    }
}
