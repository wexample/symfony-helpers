<?php

namespace Wexample\SymfonyHelpers\Service\Entity\Traits;

use App\Wex\BaseBundle\Translation\Translator;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\ArrayShape;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;

trait WithMessageEntityServiceTrait
{
    abstract public function getTranslator(): Translator;

    #[ArrayShape(['type' => 'string', 'key' => 'string', 'message' => 'string'])]
    public function buildEntityMessage(
        string $type,
        string $message,
        array $args = []
    ) {
        return [
            'type' => $type,
            'key' => $message,
            'message' => $this->getTranslator()->trans(
                'entity.'.$this->getEntityTableizedName().Translator::DOMAIN_SEPARATOR.'message.'.$type.'.'.$message,
                $args
            ),
        ];
    }

    /**
     * @throws NonUniqueResultException
     */
    public function queryForEntitiesWithMessages(
        array $entities,
        string $messageLevel = null,
        QueryBuilder $builder = null
    ): QueryBuilder {
        $ids = [];

        /** @var AbstractEntityInterface $entity */
        foreach ($entities as $entity) {
            if ($this->buildEntityMessages(
                $entity,
                $messageLevel
            )) {
                $ids[] = $entity->getId();
            }
        }

        $repo = $this->getEntityRepository();

        return $repo->queryForEntitiesIds($ids, $builder);
    }

    abstract public function buildEntityMessages(
        AbstractEntityInterface $entity,
        string $messageLevel = null
    ): array;

    // abstract public function getEntityRepository(): AbstractRepository;

    public function filterEntityMessages(
        array $messages,
        string $level = null
    ): array {
        // May be used by default.
        if (!$level) {
            return $messages;
        }

        $output = [];
        foreach ($messages as $message) {
            if ($message['type'] === $level) {
                $output[] = $message;
            }
        }

        return $output;
    }
}
