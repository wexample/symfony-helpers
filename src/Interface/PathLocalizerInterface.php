<?php

namespace Wexample\SymfonyHelpers\Interface;

/**
 * Where a path written once is actually reached: an application translating
 * its urls serves `/tunnels/plan/` at `/en/tunnels/plan/`, `/fr/tunnels/plan/`…
 * Whoever states paths for the outside — robots.txt — asks it, so that a rule
 * written for an app without languages still holds once they are turned on.
 */
interface PathLocalizerInterface
{
    public const string TAG = 'wexample.path_localizer';

    /**
     * @return string[] every path the given one is reached at; the path itself
     *                  when it stays as written
     */
    public function localizePath(string $path): array;
}
