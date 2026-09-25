<?php

namespace Wexample\SymfonyHelpers\Service;

use Symfony\Component\Process\Exception\ExceptionInterface;
use Symfony\Component\Process\Process;

/**
 * Reads where a git repository stands, and never gets in the way of anything.
 *
 * Built to be asked often by something that is only looking — a page showing
 * where a repository stands: git is run with no optional lock, so a status
 * never writes the index a commit is about to take; every call has a deadline
 * past which git is killed; and any failure answers null rather than a guess.
 * On a slow disk — a repository mounted into Docker from Windows — that is the
 * difference between a late answer and a stuck page.
 */
final readonly class GitRepositoryReader
{
    /** Seconds a single git call may take before it is killed. */
    private const int TIMEOUT = 10;

    /**
     * The branch checked out (or the short hash of a detached HEAD), the full
     * hash of that commit, and how many paths differ from it, untracked ones
     * included — in one call.
     *
     * @return array{branch: string, head: string|null, changes: int}|null
     */
    public function status(string $repository): ?array
    {
        $output = $this->git($repository, ['status', '--porcelain=v2', '--branch']);

        if (null === $output) {
            return null;
        }

        $branch = null;
        $head = null;
        $changes = 0;

        foreach (explode("\n", $output) as $line) {
            if (str_starts_with($line, '# branch.head ')) {
                $branch = substr($line, strlen('# branch.head '));
            } elseif (str_starts_with($line, '# branch.oid ')) {
                $oid = substr($line, strlen('# branch.oid '));
                $head = '(initial)' === $oid ? null : $oid;
            } elseif ('' !== $line && ! str_starts_with($line, '#')) {
                ++$changes;
            }
        }

        if (null === $branch) {
            return null;
        }

        // Detached: named by the commit it stands on, as git names it.
        if ('(detached)' === $branch) {
            $branch = null === $head ? $branch : substr($head, 0, 7);
        }

        return ['branch' => $branch, 'head' => $head, 'changes' => $changes];
    }

    /**
     * A commit's short hash and the first line of its message.
     *
     * @return array{hash: string, subject: string}|null
     */
    public function commit(string $repository, string $revision): ?array
    {
        $output = $this->git($repository, ['log', '-1', '--format=%h%x00%s', $revision]);

        if (null === $output || ! str_contains($output, "\0")) {
            return null;
        }

        [$hash, $subject] = explode("\0", $output, 2);

        return ['hash' => $hash, 'subject' => $subject];
    }

    /**
     * Whether git is at work in that repository right now: a commit, a checkout
     * or an add holds the index lock, and reading beside it is reading a state
     * about to change.
     */
    public function isBusy(string $repository): bool
    {
        return is_file($repository.'/.git/index.lock');
    }

    private function git(string $repository, array $arguments): ?string
    {
        if (! is_dir($repository.'/.git') && ! is_file($repository.'/.git')) {
            return null;
        }

        $process = new Process(
            array_merge(
                // A repository owned by someone else is refused otherwise, and
                // the container runs git as whoever runs PHP.
                ['git', '--no-optional-locks', '-c', 'safe.directory='.$repository, '-c', 'core.fsmonitor=false', '-C', $repository],
                $arguments
            ),
            env: [
                'GIT_OPTIONAL_LOCKS' => '0',
                'GIT_TERMINAL_PROMPT' => '0',
                'LC_ALL' => 'C',
            ],
            timeout: self::TIMEOUT,
        );

        try {
            $process->run();
        } catch (ExceptionInterface) {
            // Timed out and killed, or could not be started: nothing is known.
            return null;
        }

        return $process->isSuccessful() ? rtrim($process->getOutput(), "\n") : null;
    }
}
