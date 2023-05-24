<?php

namespace Wexample\SymfonyHelpers\Entity\Interfaces;

interface AbstractEntityInterface
{
    /**
     * @return ?int
     */
    public function getId(): ?int;

    public function setId(int $id);
}
