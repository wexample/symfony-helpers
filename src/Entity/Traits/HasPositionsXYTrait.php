<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

trait HasPositionsXYTrait
{
    use HasPositionXTrait;
    use HasPositionYTrait;

    public function setPositionXY(
        ?int $positionX,
        ?int $positionY
    ) {
        $this->setPositionX($positionX);
        $this->setPositionY($positionY);
    }
}
