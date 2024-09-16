<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use JetBrains\PhpStorm\Pure;
use Wexample\SymfonyHelpers\Helper\VariableHelper;
use function in_array;

trait HasStatusTrait
{
    use HasLimitedValuesPropertyTrait;

    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 30)]
    protected string $status;

    abstract public static function getStatusesList(): array;

    abstract public static function getStatusDefault(): string;

    #[Pure]
    public function hasStatus($status): bool
    {
        return $this->getStatus() === $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->checkAllowedOrFail(
            $status,
            $this->getStatusesList()
        );

        $this->status = $status;
    }

    #[Pure]
    public function hasSomeStatus(array $statuses): bool
    {
        return in_array($this->getStatus(), $statuses);
    }
}
