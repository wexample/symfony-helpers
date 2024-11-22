<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait UserEntityTrait
{
    use BaseEntityTrait;

    #[Id]
    #[Column(type: VariableHelper::VARIABLE_TYPE_INTEGER)]
    #[GeneratedValue]
    protected $id;

    #[Regex(pattern: '/^[a-z0-9][a-z0-9_-]{2,28}[a-z0-9]$/i')]
    protected $username;

    #[Length(max: 180)]
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * TODO Not typed for compatibility with old FOSUSer class.
     * @param $username
     * @return void
     */
    public function setUsername($username)
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
