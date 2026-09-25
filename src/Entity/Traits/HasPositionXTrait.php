<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;

trait HasPositionXTrait
{
    #[Column(nullable: true)]
    protected ?int $positionX = null;

    public function getPositionX(): ?int
    {
        return $this->positionX;
    }

    public function setPositionX(?int $positionX): static
    {
        $this->positionX = $positionX;

        return $this;
    }
}
