<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

trait LinkedToUserTrait
{
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: "user_id", nullable: false)]
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
