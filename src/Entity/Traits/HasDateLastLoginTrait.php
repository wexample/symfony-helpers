<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasDateLastLoginTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_DATETIME)]
    private ?DateTimeInterface $dateLastLogin = null;

    public function getDateLastLogin(): ?DateTimeInterface
    {
        return $this->dateLastLogin;
    }

    public function setDateLastLoginNow(): self
    {
        $this->setDateLastLogin(new DateTime());

        return $this;
    }

    public function setDateLastLogin(DateTimeInterface $dateLastLogin): self
    {
        $this->dateLastLogin = $dateLastLogin;

        return $this;
    }
}
