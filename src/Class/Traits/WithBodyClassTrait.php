<?php

namespace Wexample\SymfonyHelpers\Class\Traits;

trait WithBodyClassTrait
{
    protected ?string $body;

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }
}