<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints\NotBlank;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasVectorDataTrait
{
    #[NotBlank]
    #[Column(type: VariableHelper::VARIABLE_TYPE_BLOB, nullable: false)]
    protected ?string $vectorData = null;

    public function getVectorData(): ?string
    {
        return $this->vectorData;
    }

    public function setVectorData(string $vectorData): static
    {
        $this->vectorData = $vectorData;

        return $this;
    }
}
