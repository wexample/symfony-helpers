<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait UserWithNameTrait
{
    use UserEntityTrait;
    use HasDisplayName;

    #[Type(type: VariableHelper::VARIABLE_TYPE_STRING)]
    #[Length(max: 100)]
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 100, nullable: true)]
    private ?string $firstName = null;

    #[Type(type: VariableHelper::VARIABLE_TYPE_STRING)]
    #[Length(max: 100)]
    #[Column(type: VariableHelper::VARIABLE_TYPE_STRING, length: 100, nullable: true)]
    private ?string $lastName = null;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getDisplayName(bool $short = false): string
    {
        $firstName = $this->getFirstName();
        $lastName = $short ? null : $this->getLastName();

        return $firstName
            ? implode(' ', [$firstName, $lastName])
            : $this->getUsername();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->getDisplayName();
    }
}
