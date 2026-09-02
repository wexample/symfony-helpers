<?php

namespace Wexample\SymfonyHelpers\Entity\Interfaces;

use Symfony\Component\Uid\Uuid;

interface AbstractEntityInterface
{
    public function getId(): Uuid;

    public function setId(Uuid $id);
}
