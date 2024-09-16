<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasDateLastAccessTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_DATETIME)]
    private ?DateTimeInterface $dateLastAccess = null;

    public function getDateLastAccess(): ?DateTimeInterface
    {
        return $this->dateLastAccess;
    }

    public function setDateLastLoginNow(): self
    {
        $this->setDateLastAccess(new DateTime());

        return $this;
    }

    public function setDateLastAccess(DateTimeInterface $dateLastAccess): self
    {
        $this->dateLastAccess = $dateLastAccess;

        return $this;
    }
}
