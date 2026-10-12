<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Wexample\SymfonyHelpers\Entity\Interfaces\UserEntityInterface;

/**
 * The host app maps the interface to its own user class:
 * doctrine.orm.resolve_target_entities: { UserEntityInterface: App\Entity\User }.
 */
trait LinkedToUserTrait
{
    #[ManyToOne(targetEntity: UserEntityInterface::class)]
    #[JoinColumn(name: "user_id", nullable: false)]
    protected ?UserEntityInterface $user = null;

    public function getUser(): ?UserEntityInterface
    {
        return $this->user;
    }

    public function setUser(?UserEntityInterface $user): void
    {
        $this->user = $user;
    }
}
