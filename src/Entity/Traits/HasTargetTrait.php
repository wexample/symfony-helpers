<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HasTargetTrait
{
    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $target = null;

    public function getTarget(): ?self
    {
        return $this->target;
    }

    public function setTarget(?self $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function resolveTarget(): static
    {
        if ($target = $this->getTarget()) {
            return $target->resolveTarget();
        }

        return $this;
    }
}
