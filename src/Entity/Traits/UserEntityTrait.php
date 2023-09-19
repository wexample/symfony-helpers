<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

trait UserEntityTrait
{
    use BaseEntityTrait;

    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    protected $id;

    /**
     * @var string
     */
    #[Regex(pattern: '/^[a-z0-9][a-z0-9_-]{2,28}[a-z0-9]$/i')]
    protected string $username;

    /**
     * @return string
     */
    #[Length(max: 180)]
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * TODO Symfony / FOS user polyfill.
     */
    public function isAccountNonLocked(): bool
    {
        return true;
    }
}
