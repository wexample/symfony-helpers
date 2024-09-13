<?php

namespace App\Wex\BaseBundle\Entity\Traits;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Column;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasDateCreatedTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_DATETIME)]
    private ?DateTimeInterface $dateCreated = null;

    public function getDateCreated(): ?DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreatedNow(): self
    {
        $this->setDateCreated(new DateTime());

        return $this;
    }

    public function setDateCreated(DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }
}
