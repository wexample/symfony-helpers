<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use Wexample\SymfonySearch\Attribute\SearchText;

trait HasNameTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 255)]
    // Inert where `wexample/symfony-search` is not installed, which is why this
    // package can name it without requiring it — the api requires this one and
    // search requires the api, so the dependency would close a circle.
    #[SearchText]
    protected ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setNameFromTitle(): self
    {
        $this->setName(
            TextHelper::slugify(
                $this->getTitle()
            )
        );

        return $this;
    }
}
