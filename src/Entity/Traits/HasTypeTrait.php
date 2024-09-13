<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

trait HasTypeTrait
{
    #[Column(type: 'string', length: 120)]
    protected ?string $type = null;

    /**
     * Override this method to return an array of allowed types.
     * @return array|null
     */
    public function getAllowedTypes(): ?array
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
    public function setType($type): void
    {
        $allowedTypes = $this->getAllowedTypes();

        // Check if allowed types are defined and if the type is in the allowed list
        if (is_array($allowedTypes) && !in_array($type, $allowedTypes, true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid type "%s". Allowed types are: %s',
                $type,
                implode(', ', $allowedTypes)
            ));
        }

        $this->type = $type;
    }

    #[Pure]
    public function isOfType(string $type): bool
    {
        return $this->getType() === $type;
    }
}
