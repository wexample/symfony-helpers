<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints\NotBlank;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasEmbeddingTrait
{
    #[NotBlank]
    #[Column(type: VariableHelper::VARIABLE_TYPE_BLOB, nullable: false)]
    protected ?string $embedding = null;

    public function getEmbedding(): ?string
    {
        return $this->embedding;
    }

    public function setEmbedding(string $embedding): static
    {
        $this->embedding = $embedding;

        return $this;
    }
}
