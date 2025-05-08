<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;

trait HasPositionYTrait
{
    #[Column(nullable: true)]
    private ?int $positionY = null;

    public function getPositionY(): ?int
    {
        return $this->positionY;
    }

    public function setPositionY(?int $positionY): static
    {
        $this->positionY = $positionY;

        return $this;
    }
}
