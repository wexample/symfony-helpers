<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Mapping\Column;
use JetBrains\PhpStorm\Pure;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait LinkedToEntityTrait
{
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 255, nullable: true)]
    private ?string $entityType = null;

    #[Column(type: VariableHelper::VARIABLE_TYPE_INTEGER, nullable: true)]
    private ?int $entityId = null;

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public function setEntityType(?string $entityType): self
    {
        $this->entityType = $entityType;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function setLinkedEntity(AbstractEntityInterface $abstractEntity): self
    {
        $this->setEntityType(
            ClassUtils::getClass($abstractEntity)
        );
        $this->setEntityId($abstractEntity->getId());

        return $this;
    }

    #[Pure]
    public function hasLinkedEntity(): bool
    {
        return $this->getEntityId() && $this->getEntityType();
    }
}
