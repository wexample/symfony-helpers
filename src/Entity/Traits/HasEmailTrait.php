<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Email;
use Wexample\SymfonyHelpers\Helper\TextHelper;

trait HasEmailTrait
{
    #[ORM\Column(type: 'string', length: 255)]
    #[Email()]
    protected ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function buildEmailString(): ?string
    {
        return TextHelper::emailString(
            $this->getEmail(),
            $this->getEmailName()
        );
    }

    /**
     * Not typed to match with FOSUser setEmail.
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Name used for the Name<email@address.com> pattern.
     */
    abstract public function getEmailName(): string;
}
