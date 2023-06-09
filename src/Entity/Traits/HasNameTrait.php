<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\TextHelper;

trait HasNameTrait
{
    #[Column(type: 'string', length: 255)]
    protected ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
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
