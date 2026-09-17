<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use Wexample\SymfonySearch\Attribute\SearchText;

trait HasBodyTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_TEXT, nullable: true)]
    // Inert where `wexample/symfony-search` is not installed, which is why this
    // package can name it without requiring it — the api requires this one and
    // search requires the api, so the dependency would close a circle.
    #[SearchText(points: 5)]
    protected ?string $body = null;

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }
}
