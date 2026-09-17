<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonySearch\Attribute\SearchText;

trait HasDescriptionTrait
{
    #[Column(type: Types::TEXT, nullable: true)]
    // Inert where `wexample/symfony-search` is not installed, which is why this
    // package can name it without requiring it — the api requires this one and
    // search requires the api, so the dependency would close a circle.
    #[SearchText(points: 5)]
    protected ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
