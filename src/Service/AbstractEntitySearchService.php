<?php

namespace Wexample\SymfonyHelpers\Service;

use App\Entity\SearchResult;
use App\Wex\BaseBundle\Service\Search\SearchEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\EntityHelper;
use function array_merge;
use function array_splice;
use function trim;
use function uasort;

abstract class AbstractEntitySearchService
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected \Symfony\Bundle\SecurityBundle\Security $security
    ) {
    }

    public static function createEntitiesSearchResults(array $entities): array
    {
        $output = [];

        foreach ($entities as $entity) {
            $output[] = self::createEntitySearchResult($entity);
        }

        return $output;
    }

    public function search(
        string $searchAction,
        string $string,
        int $maxResults = 5,
        ?string $entity = null
    ) {
        $string = trim($string);

        // Anonymous search is not allowed.
        if ('' === $string || !$this->security->getUser()) {
            return [];
        }

        $searchEntityServices = $this->getSearchEntityServices();

        if ($entity) {
            $searchEntityServicesFiltered = [];
            $entityFullName = ClassHelper::fullEntityClassPathFromEntityPath($entity);

            /** @var SearchEntityService $searchEntityService */
            foreach ($searchEntityServices as $searchEntityService) {
                if ($searchEntityService::manipulatesEntity($entityFullName)) {
                    $searchEntityServicesFiltered[] = $searchEntityService;
                }
            }

            $searchEntityServices = $searchEntityServicesFiltered;
        }

        $results = [];

        if (!$string) {
            return $results;
        }

        /** @var SearchEntityService $searchEntityService */
        foreach ($searchEntityServices as $searchEntityService) {
            $serviceResults = [];

            if ($searchEntityService->allowSearchAction($searchAction)) {
                $builder = $this
                    ->em
                    ->createQueryBuilder();

                if ($maxResults > 0) {
                    // Get the double of expected results,
                    // before setting scores and sorting.
                    $builder->setMaxResults($maxResults * 2);
                }

                $searchEntityService
                    ->selectEntity($builder)
                    ->search(
                        $builder,
                        $searchAction,
                        $string
                    );

                $entityResults = $builder->getQuery()->execute();

                /** @var AbstractEntity $entity */
                foreach ($entityResults as $entity) {
                    $searchResult =
                        self::createEntitySearchResult($entity);
                    $searchResult->score =
                        $searchEntityService->searchResultBuildScore(
                            $entity,
                            $searchAction,
                            $string
                        );
                    $serviceResults[] =
                        $searchResult;
                }
            }

            // Sort service results.
            $this->sortResults($serviceResults);

            // Only keep the [maxResults] first for this service.
            $results = array_merge(
                $results,
                array_splice($serviceResults, 0, $maxResults)
            );
        }

        // Sort final merged array.
        $this->sortResults($results);

        return $maxResults > 0
            ? array_splice($results, 0, $maxResults)
            : $results;
    }

    abstract public function getSearchEntityServices(): array;

    public static function createEntitySearchResult(AbstractEntityInterface $entity): SearchResult
    {
        $searchResult =
            new SearchResult();
        $searchResult->entity =
            $entity;
        $searchResult->id =
            EntityHelper::createEntityId($entity);

        $searchResult->setEntityId(
            $entity->getId()
        );

        $searchResult->setEntityTypeFromClassName(
            $entity::class
        );

        return $searchResult;
    }

    protected function sortResults(array &$results)
    {
        uasort(
            $results,
            fn($a, $b): int => $a->score < $b->score
        );
    }
}
