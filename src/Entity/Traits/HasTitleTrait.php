<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints\NotBlank;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasTitleTrait
{
    #[NotBlank]
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 255)]
    protected ?string $title = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
