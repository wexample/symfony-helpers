<?php

namespace Wexample\SymfonyHelpers\Helper;

use DateTime;
use Wexample\PhpDate\Helper\DateHelper;

class GitHelper
{
    public static function getTagsForLastCommit(string $repo = null): array
    {
        if (is_null($repo)) {
            $repo = getcwd();
        }

        $repoGit = realpath($repo).'/.git/';

        if (! is_dir($repoGit)) {
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

    /**
     * The branch checked out, the short hash of the commit when none is (a
     * detached HEAD), or null when the directory is no repository.
     */
    public static function getCurrentBranch(string $repo): ?string
    {
        $branch = self::readGit($repo, ['rev-parse', '--abbrev-ref', 'HEAD']);

        if ('HEAD' === $branch) {
            return self::readGit($repo, ['rev-parse', '--short', 'HEAD']);
        }

        return $branch;
    }

    /**
     * How many paths differ from the last commit, untracked ones included: what
     * a commit made now would take, or leave behind. Null when the directory is
     * no repository.
     */
    public static function countUncommittedChanges(string $repo): ?int
    {
        $status = self::readGit($repo, ['status', '--porcelain'], allowEmpty: true);

        if (null === $status) {
            return null;
        }

        return '' === $status ? 0 : count(explode(PHP_EOL, $status));
    }

    /**
     * Git run by whoever runs PHP, the repository declared safe for that one
     * call: a repository owned by someone else is refused otherwise, and
     * switching user only works for root. Null when git fails.
     */
    private static function readGit(string $repo, array $arguments, bool $allowEmpty = false): ?string
    {
        if (! is_dir($repo)) {
            return null;
        }

        $command = array_merge(
            ['git', '-c', 'safe.directory='.$repo, '-C', $repo],
            $arguments
        );

        $output = [];
        $code = 0;
        exec(implode(' ', array_map('escapeshellarg', $command)).' 2>/dev/null', $output, $code);

        if (0 !== $code) {
            return null;
        }

        $result = trim(implode(PHP_EOL, $output));

        return '' === $result && ! $allowEmpty ? null : $result;
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
                if (! empty($commit)) {
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
                    DateHelper::DATE_PATTERN_TIME_DEFAULT,
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
