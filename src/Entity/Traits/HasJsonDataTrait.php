<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use stdClass;

trait HasJsonDataTrait
{
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?string $jsonData = null;

    public function getJsonData(): array|null|stdClass
    {
        return $this->jsonData ? json_decode($this->jsonData, null, 512, JSON_THROW_ON_ERROR) : null;
    }

    public function setJsonData(array|null|stdClass $jsonData): self
    {
        if (is_array($jsonData) || is_object($jsonData)) {
            $jsonData = json_encode($jsonData, JSON_THROW_ON_ERROR);
        }

        $this->jsonData = $jsonData;

        return $this;
    }
}
