<?php

namespace Wexample\SymfonyHelpers\Helper;

class GitHelper
{
    public static function getTagsForLastCommit(string $repo = null): array
    {
        if (is_null($repo)) {
            $repo = getcwd();
        }

        $repoGit = realpath($repo).'/.git/';

        if (!is_dir($repoGit)) {
            return [];
        }

        $fileOwner = SystemHelper::getFileOwner($repoGit);

        $lastCommitHash = SystemHelper::exec(
            ['git', 'rev-parse', 'HEAD'],
            $repo,
            $fileOwner,
        );

        $tags = SystemHelper::exec(
            ['git', 'tag', '--contains', $lastCommitHash],
            $repo,
            $fileOwner,
        );

        return $tags ? explode(PHP_EOL, $tags) : [];
    }
}
