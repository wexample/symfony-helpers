<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasGroupTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 255)]
    protected ?string $group = null;

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function setGroup(?string $group): static
    {
        $this->group = $group;

        return $this;
    }
}
