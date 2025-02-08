<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasNameTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 255)]
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
