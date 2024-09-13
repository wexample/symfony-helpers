<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasPositionTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_INTEGER)]
    protected ?int $position = null;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function incrementPosition(): self
    {
        $this->position = ($this->position ?? 0) + 1;

        return $this;
    }

    public function decrementPosition(): self
    {
        $this->position = ($this->position ?? 0) - 1;

        return $this;
    }
}
