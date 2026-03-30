<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use Doctrine\ORM\Mapping\Entity;
use ReflectionAttribute;
use ReflectionClass;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

class RepositoryRule extends AbstractRectifyRule
{
    /**
     * @return string[]
     */
    public function apply(
        ReflectionClass $entityReflection
    ): array {
        $repositoryClass = $this->resolveRepositoryClass($entityReflection);

        if (! class_exists($repositoryClass)) {
            return [
                sprintf(
                    '%s must have repository %s.',
                    $entityReflection->getName(),
                    $repositoryClass
                ),
            ];
        }

        if (! is_subclass_of($repositoryClass, AbstractRepository::class, true)) {
            $repositoryReflection = new ReflectionClass($repositoryClass);
            $repositoryParentClass = $repositoryReflection->getParentClass();

            if ($repositoryParentClass !== false && $repositoryParentClass->getName() === 'Doctrine\\Bundle\\DoctrineBundle\\Repository\\ServiceEntityRepository') {
                $this->rectifyInitialSymfonyRepository(
                    $entityReflection,
                    $repositoryReflection
                );

                return [];
            }

            return [
                sprintf(
                    '%s repository %s must extend %s.',
                    $entityReflection->getName(),
                    $repositoryClass,
                    AbstractRepository::class
                ),
            ];
        }

        return [];
    }

    private function rectifyInitialSymfonyRepository(
        ReflectionClass $entityReflection,
        ReflectionClass $repositoryReflection
    ): void {
        $filePath = $repositoryReflection->getFileName();
        $content = file_get_contents($filePath);

        $content = str_replace(
            "use Doctrine\\Bundle\\DoctrineBundle\\Repository\\ServiceEntityRepository;\n",
            '',
            $content
        );
        $content = str_replace(
            "use Doctrine\\Persistence\\ManagerRegistry;\n",
            '',
            $content
        );

        if (! str_contains($content, 'use Wexample\\SymfonyHelpers\\Repository\\AbstractRepository;')) {
            $content = preg_replace(
                '/^namespace\s+[^;]+;\n/m',
                "$0\nuse Wexample\\SymfonyHelpers\\Repository\\AbstractRepository;\n",
                $content,
                1
            );
        }

        $repositoryShortName = $repositoryReflection->getShortName();
        $content = preg_replace(
            '/\bclass\s+'.preg_quote($repositoryShortName, '/').'\s+extends\s+[^\s{]+/m',
            'class '.$repositoryShortName.' extends AbstractRepository',
            $content,
            1
        );

        $content = preg_replace(
            '/\n\/\*\*\s*\n\s*\*\s*@extends ServiceEntityRepository<[^>]+>\s*\n\s*\*\/\n/m',
            "\n",
            $content,
            1
        );

        $content = $this->removeInitialSymfonyRepositoryBodyIfNeeded(
            $content,
            $repositoryReflection->getShortName(),
            $entityReflection->getShortName()
        );

        file_put_contents($filePath, $content);
    }

    private function removeInitialSymfonyRepositoryBodyIfNeeded(
        string $content,
        string $repositoryShortName,
        string $entityShortName
    ): string {
        $alias = strtolower($entityShortName[0]);
        $pattern = '/(class\s+'.preg_quote($repositoryShortName, '/').'\b[^{]*\{)(?<body>[\s\S]*?)(^\})/m';
        if (! preg_match($pattern, $content, $matches)) {
            return $content;
        }

        $normalizedBody = preg_replace('/\s+/', '', $matches['body']);
        $normalizedSymfonyDefaultBody = preg_replace('/\s+/', '', <<<PHP
public function __construct(ManagerRegistry \$registry)
{
    parent::__construct(\$registry, {$entityShortName}::class);
}

//    /**
//     * @return {$entityShortName}[] Returns an array of {$entityShortName} objects
//     */
//    public function findByExampleField(\$value): array
//    {
//        return \$this->createQueryBuilder('{$alias}')
//            ->andWhere('{$alias}.exampleField = :val')
//            ->setParameter('val', \$value)
//            ->orderBy('{$alias}.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField(\$value): ?{$entityShortName}
//    {
//        return \$this->createQueryBuilder('{$alias}')
//            ->andWhere('{$alias}.exampleField = :val')
//            ->setParameter('val', \$value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
PHP);

        if ($normalizedBody !== $normalizedSymfonyDefaultBody) {
            return $content;
        }

        return preg_replace(
            $pattern,
            '$1'."\n".'$3',
            $content,
            1
        );
    }

    private function resolveRepositoryClass(
        ReflectionClass $entityReflection
    ): string {
        $entityAttributes = $entityReflection->getAttributes(
            Entity::class,
            ReflectionAttribute::IS_INSTANCEOF
        );

        if ($entityAttributes !== []) {
            /** @var Entity $entityAttribute */
            $entityAttribute = $entityAttributes[0]->newInstance();
            if ($entityAttribute->repositoryClass) {
                return $entityAttribute->repositoryClass;
            }
        }

        return sprintf(
            'App\\Repository\\%sRepository',
            $entityReflection->getShortName()
        );
    }
}
