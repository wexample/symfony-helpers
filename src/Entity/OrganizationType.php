<?php

namespace Wexample\SymfonyHelpers\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Stringable;

class OrganizationType extends AbstractEntity implements Stringable
{
    #[Column(type: Types::STRING, length: 255)]
    protected ?string $name;

    #[Column(type: Types::STRING, length: 255)]
    protected ?string $title = null;

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
