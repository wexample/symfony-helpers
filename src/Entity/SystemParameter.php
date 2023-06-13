<?php

namespace Wexample\SymfonyHelpers\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;

abstract class SystemParameter extends AbstractEntity
{
    use HasNameTrait;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $value;

    public function getValue(?string $default = null): ?string
    {
        return $this->value !== null ? $this->value : $default;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
