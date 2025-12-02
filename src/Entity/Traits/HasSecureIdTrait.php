<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasSecureIdTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 255)]
    protected string $secureId;

    public static function getSecureIdPrefix(): ?string
    {
        return null;
    }

    public function getSecureId(): ?string
    {
        return $this->secureId;
    }

    public function setSecureId(string $secureId): self
    {
        $this->secureId = $secureId;

        return $this;
    }

    public function setOrGenerateSecureId(?string $secureId = null): self
    {
        if ($secureId === null) {
            return $this->setGeneratedSecureId();
        } else {
            return $this->setSecureId($secureId);
        }
    }

    public static function buildSecureId(): string
    {
        return TextHelper::generateSecureId(prefix: static::getSecureIdPrefix());
    }

    public function setGeneratedSecureId(): self
    {
        $this->secureId = static::buildSecureId();

        return $this;
    }
}
