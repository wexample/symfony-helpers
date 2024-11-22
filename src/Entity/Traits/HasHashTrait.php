<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasHashTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 255)]
    protected string $hash;

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    public function buildHash(): void
    {
        $this->hash = hash('sha256', uniqid(rand(), true));
    }
}
