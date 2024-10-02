<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;

trait HasFamilyTrait
{
    #[Column(length: 255)]
    protected ?string $family = null;

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(string $family): static
    {
        $this->family = $family;

        return $this;
    }
}
