<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasContentTypeTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 30)]
    protected ?string $contentType = null;

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(?string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }
}
