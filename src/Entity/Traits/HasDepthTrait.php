<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasDepthTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_INTEGER)]
    protected ?int $depth = null;

    public function getDepth(): ?int
    {
        return $this->depth;
    }

    public function setDepth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }
}
