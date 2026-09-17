<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints\NotBlank;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use Wexample\SymfonySearch\Attribute\SearchText;

trait HasTitleTrait
{
    #[NotBlank]
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 255)]
    // Inert where `wexample/symfony-search` is not installed, which is why this
    // package can name it without requiring it — the api requires this one and
    // search requires the api, so the dependency would close a circle.
    #[SearchText]
    protected ?string $title = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
