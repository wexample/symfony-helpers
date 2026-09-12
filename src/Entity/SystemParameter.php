<?php

namespace Wexample\SymfonyHelpers\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;

/**
 * SystemParameter Class
 *
 * This is an abstract class that represents a system parameter in the application.
 * System parameters are key-value pairs that are used for storing system configuration settings.
 *
 * @deprecated use `Wexample\SymfonyPlatform\Entity\Configuration`, which holds a
 *             JSON value rather than a string, is read from a record like every
 *             other row, and ships its repository, its DTO and its front class
 *             instead of leaving each app to write them again.
 */
abstract class SystemParameter extends AbstractEntity
{
    use HasNameTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $value;

    public function getValue(string $default = null): ?string
    {
        return null !== $this->value ? $this->value : $default;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
