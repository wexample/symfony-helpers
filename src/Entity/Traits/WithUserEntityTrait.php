<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Wexample\SymfonyHelpers\Entity\User;

trait WithUserEntityTrait
{
    use BaseEntityTrait;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn]
    protected ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser(?User $user)
    {
        $this->user = $user;
    }
}
