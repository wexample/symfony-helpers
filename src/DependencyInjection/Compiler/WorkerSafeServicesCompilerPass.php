<?php

namespace Wexample\SymfonyHelpers\DependencyInjection\Compiler;

use LogicException;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Contracts\Service\ResetInterface;
use Wexample\SymfonyHelpers\Attribute\WorkerSafe;

/**
 * A worker (FrankenPHP) keeps one container for many requests: whatever a
 * service holds outlives the request that put it there, and reaches the next
 * visitor. Every shared service of ours is therefore one of three things:
 *
 *  - stateless by construction: each property it declares is readonly (or a
 *    virtual one, read from a hook), which PHP itself enforces;
 *  - reset between requests: it implements ResetInterface, its reset() emptying
 *    what a request filled;
 *  - declared safe, the service or the property marked #[WorkerSafe] with the
 *    reason — a memo of what the code or the configuration says.
 *
 * Checked on the services the container keeps, ours only: a property declared
 * by a vendor class is the vendor's. The `wexample_symfony_helpers.worker_safety`
 * parameter decides what a breach does: `strict` stops the build, `warn` (the
 * default) lists the breaches in the `.violations` parameter and the compiler
 * log, `off` skips the check.
 *
 * Not seen from here: a readonly property holding an object that changes, a
 * constructor reading the request. Those are what the reuse tests are for.
 */
class WorkerSafeServicesCompilerPass implements CompilerPassInterface
{
    public const string PARAMETER_MODE = 'wexample_symfony_helpers.worker_safety';
    public const string PARAMETER_NAMESPACES = 'wexample_symfony_helpers.worker_safety.namespaces';
    public const string PARAMETER_VIOLATIONS = 'wexample_symfony_helpers.worker_safety.violations';

    public const string MODE_STRICT = 'strict';
    public const string MODE_WARN = 'warn';
    public const string MODE_OFF = 'off';

    private const array DEFAULT_NAMESPACES = ['Wexample\\', 'App\\'];

    public function process(ContainerBuilder $container): void
    {
        $mode = $container->hasParameter(self::PARAMETER_MODE)
            ? $container->getParameter(self::PARAMETER_MODE)
            : self::MODE_WARN;

        if (self::MODE_OFF === $mode) {
            return;
        }

        $namespaces = $container->hasParameter(self::PARAMETER_NAMESPACES)
            ? $container->getParameter(self::PARAMETER_NAMESPACES)
            : self::DEFAULT_NAMESPACES;

        $violations = [];
        $checked = [];

        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();

            // A service built anew at each use keeps nothing between two; an
            // errored definition — a controller argument typed with an entity
            // or a DTO — is never built at all.
            if (! $class
                || $definition->isAbstract()
                || $definition->isSynthetic()
                || ! $definition->isShared()
                || $definition->hasErrors()
                || isset($checked[$class])
                || ! $this->isOurs($class, $namespaces)
                || ! class_exists($class)
            ) {
                continue;
            }

            $checked[$class] = true;

            // A command runs once per process, from the console: no worker
            // serving web requests ever executes it.
            if (is_subclass_of($class, Command::class)) {
                continue;
            }

            foreach ($this->findViolations(new ReflectionClass($class), $namespaces) as $violation) {
                $violations[] = $violation;
            }
        }

        $violations = array_values(array_unique($violations));
        sort($violations);
        $container->setParameter(self::PARAMETER_VIOLATIONS, $violations);

        if ([] === $violations) {
            return;
        }

        $message = sprintf(
            "%d service propert%s may carry a request's state to the next one a worker serves:\n  - %s\n"
            ."Make each readonly, empty it in reset() (ResetInterface), or mark it #[WorkerSafe('why')] "
            .'when it only memoizes what the code or the configuration says.',
            count($violations),
            1 === count($violations) ? 'y' : 'ies',
            implode("\n  - ", $violations)
        );

        if (self::MODE_STRICT === $mode) {
            throw new LogicException($message);
        }

        $container->log($this, $message);
    }

    /**
     * @return list<string>
     */
    private function findViolations(ReflectionClass $class, array $namespaces): array
    {
        if ($class->implementsInterface(ResetInterface::class) || $this->isDeclaredSafe($class)) {
            return [];
        }

        $violations = [];

        foreach ($class->getProperties() as $property) {
            if ($this->isPropertySafe($property, $namespaces)) {
                continue;
            }

            $violations[] = sprintf(
                '%s::%s$%s',
                $property->getDeclaringClass()->getName(),
                $property->isStatic() ? 'static ' : '',
                $property->getName()
            );
        }

        return $violations;
    }

    private function isPropertySafe(ReflectionProperty $property, array $namespaces): bool
    {
        return $property->isReadOnly()
            || $property->isVirtual()
            || ! $this->isOurs($property->getDeclaringClass()->getName(), $namespaces)
            || $this->comesFromForeignTrait($property, $namespaces)
            || $this->isPropertyDeclaredSafe($property);
    }

    /**
     * PHP reports a property a trait brings as the using class's own: one
     * brought by a vendor's trait (NormalizerAwareTrait) is the vendor's.
     */
    private function comesFromForeignTrait(ReflectionProperty $property, array $namespaces): bool
    {
        $traits = $property->getDeclaringClass()->getTraits();

        while ($trait = array_shift($traits)) {
            if ($trait->hasProperty($property->getName()) && ! $this->isOurs($trait->getName(), $namespaces)) {
                return true;
            }

            array_push($traits, ...array_values($trait->getTraits()));
        }

        return false;
    }

    /**
     * Marked where it is declared, or where a parent declared it first: a
     * subclass redeclaring a static setting (`$ajax`) inherits its reason.
     */
    private function isPropertyDeclaredSafe(ReflectionProperty $property): bool
    {
        for ($class = $property->getDeclaringClass(); $class; $class = $class->getParentClass()) {
            if (! $class->hasProperty($property->getName())) {
                return false;
            }

            $declared = $class->getProperty($property->getName());
            if ($declared->getDeclaringClass()->getName() === $class->getName()
                && [] !== $declared->getAttributes(WorkerSafe::class)) {
                return true;
            }
        }

        return false;
    }

    private function isDeclaredSafe(ReflectionClass $class): bool
    {
        for ($current = $class; $current; $current = $current->getParentClass()) {
            if ([] !== $current->getAttributes(WorkerSafe::class)) {
                return true;
            }
        }

        return false;
    }

    private function isOurs(string $class, array $namespaces): bool
    {
        foreach ($namespaces as $namespace) {
            if (str_starts_with($class, $namespace)) {
                return true;
            }
        }

        return false;
    }
}
