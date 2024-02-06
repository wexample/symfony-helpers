<?php

namespace Wexample\SymfonyHelpers\Entity\Interfaces;

interface AbstractEntityInterface
{
    public function getId(): ?int;

    public function setId(int $id);
}
