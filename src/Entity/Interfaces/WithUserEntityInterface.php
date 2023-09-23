<?php

namespace Wexample\SymfonyHelpers\Entity\Interfaces;

interface WithUserEntityInterface
{
    public function getUser(): ?UserEntityInterface;

    public function setUser(?UserEntityInterface $user): void;
}
