<?php

namespace Wexample\SymfonyHelpers\Entity;

use Wexample\SymfonyHelpers\Entity\Interfaces\UserEntityInterface;
use Wexample\SymfonyHelpers\Entity\Traits\UserEntityTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractUser extends AbstractEntity implements UserEntityInterface
{
    use UserEntityTrait;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    protected $username;

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
