<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints\NotBlank;

trait HasEmbeddingTrait
{
    #[NotBlank]
    #[Column(type: "vector", nullable: false, options: ["dimension" => 1536])]
    protected array $embedding = [];

    public function getEmbedding(): array
    {
        return $this->embedding;
    }

    public function setEmbedding(array $embedding): static
    {
        $this->embedding = $embedding;

        return $this;
    }
}
