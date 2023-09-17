<?php

namespace Wexample\SymfonyHelpers\Helper;

use DateTime;

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

    public static function readLog(
        string $dir,
        null|int|DateTime $limit = 100
    ): array {
        $exec = [];
        chdir($dir);
        exec("git log --date=format-local:'%Y-%m-%d %H:%M:%S'", $exec);

        $history = [];
        $commit = [];
        $count = 0;
        foreach ($exec as $line) {
            if (str_starts_with($line, 'commit')) {
                if (!empty($commit)) {
                    $history[] = (object) $commit;

                    if (is_int($limit) && ++$count >= $limit) {
                        return $history;
                    } elseif ($limit instanceof DateTime && $commit['date'] < $limit) {
                        array_pop($history);

                        return $history;
                    }

                    $commit = [];
                }
                $commit['hash'] = trim(substr($line, strlen('commit')));
            } elseif (str_starts_with($line, 'Author')) {
                $commit['author'] = trim(substr($line, strlen('Author:')));
            } elseif (str_starts_with($line, 'Date')) {
                $commit['date'] = DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    trim(substr($line, strlen('Date:')))
                );
            } else {
                $commit['message'] ??= '';
                $commit['message'] .= trim($line);
            }
        }

        return $history;
    }
}
