<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;

trait HasDecimalsTrait
{
    #[Column(type: Types::SMALLINT)]
    protected int $decimals = 2;

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function setDecimals(int $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }
}
