<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;

trait HasFeatured
{
    #[Column]
    private ?bool $featured = false;

    public function isFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): static
    {
        $this->featured = $featured;

        return $this;
    }
}
