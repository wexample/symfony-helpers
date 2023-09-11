<?php
/**
 * Created by PhpStorm.
 * User: Illmmin
 * Date: 27/06/2018
 * Time: 11:09.
 */

namespace Wexample\SymfonyHelpers\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Entity\Traits\BaseEntityTrait;

abstract class User extends BaseUser implements AbstractEntityInterface
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
    protected $username;

    /**
     * @return string
     */
    #[Length(max: 180)]
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
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
