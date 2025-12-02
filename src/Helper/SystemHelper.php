<?php

namespace Wexample\SymfonyHelpers\Helper;

class SystemHelper
{
    public static function getFileOwner(string $path): string
    {
        return SystemHelper::exec(
            [
                'ls',
                '-ld',
                $path,
                '|',
                'awk',
                "'{print $3}'",
            ]
        );
    }

    public static function exec(
        array $commands,
        string $workingDir = null,
        string $username = null
    ): string {
        if ($workingDir) {
            $commands = array_merge(
                ['cd', escapeshellarg($workingDir), '&&'],
                $commands
            );
        }

        if ($username) {
            $commands = [
                'su',
                '-c',
                escapeshellarg(SystemHelper::joinCommands($commands)),
                '-s',
                '/bin/sh',
                $username,
            ];
        }

        return trim(
            shell_exec(
                SystemHelper::joinCommands($commands)
            )
        );
    }

    public static function joinCommands(array $commands): string
    {
        return implode(
            ' ',
            $commands
        );
    }
}
