<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasVisibilityTrait
{
    use HasLimitedValuesPropertyTrait;

    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 120)]
    protected ?string $visibility = null;

    public function getAllowedVisibilities(): ?array
    {
        return null;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): void
    {
        $this->checkAllowedOrFail(
            $visibility,
            $this->getAllowedVisibilities()
        );

        $this->visibility = $visibility;
    }
}
