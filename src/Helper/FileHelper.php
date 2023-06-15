<?php

namespace Wexample\SymfonyHelpers\Helper;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function is_dir;

class FileHelper
{
    /**
     * string.
     */
    final public const PATTERN_GLOB = 'glob';

    final public const PATH_VAR = 'var/';

    final public const EXTENSION_SEPARATOR = '.';

    final public const FOLDER_SEPARATOR = '/';

    final public const FILE_EXTENSION_PDF = 'pdf';

    final public const FILE_EXTENSION_PHP = 'php';

    final public const FILE_EXTENSION_YML = 'yml';

    final public const FILE_EXTENSION_VUE = 'vue';

    final public const FILE_EXTENSION_SVG = 'svg';

    final public const SUFFIX_AGGREGATED = 'agg';

    public static function createFileIfMissingAndGetJson(
        string $path,
        string $content = '{}',
        bool $associative = false
    ): array {
        $content = FileHelper::createFileIfMissingAndGetContent($path, $content);

        return json_decode(
            $content,
            $associative
        ) ?? [];
    }

    public static function createFileIfMissingAndGetContent(
        string $path,
        string $content = null,
    ): string {
        FileHelper::createFileIfMissing($path, $content);

        return file_get_contents($path);
    }

    public static function createFileIfMissing(
        string $path,
        string $content = null
    ): void {
        FileHelper::createDirIfMissing(dirname($path));

        if (!file_exists($path)) {
            if (!is_null($content)) {
                file_put_contents($path, $content);
            } else {
                touch($path);
            }
        }
    }

    public static function createDirIfMissing(string $path): void
    {
        if (!file_exists($path)) {
            mkdir(
                $path,
                0755,
                true
            );
        }
    }

    public static function deleteFileIfExists(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }

    public static function deleteRecursive(string $path): void
    {
        if (static::isCriticalPath($path)) {
            return;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $path,
                FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $info) {
            $method = ($info->isDir() ? 'rmdir' : 'unlink');
            $method($info->getRealPath());
        }
    }

    public static function isCriticalPath(string $path): bool
    {
        return '' === $path
            || '/' === $path
            || !is_dir($path);
    }

    public static function forEachValidFile(
        string $dir,
        callable $callback
    ): void {
        $dir .= !str_ends_with($dir, FileHelper::FOLDER_SEPARATOR)
            ? FileHelper::FOLDER_SEPARATOR : '';
        $items = scandir($dir);

        foreach ($items as $item) {
            if ('.' !== $item[0]) {
                if (is_dir($dir.$item)) {
                    static::forEachValidFile(
                        $dir.$item.FileHelper::FOLDER_SEPARATOR,
                        $callback
                    );
                } else {
                    $callback($dir.$item);
                }
            }
        }
    }

    public static function trimFirstPathChunk(
        string $path
    ): string {
        return TextHelper::trimFirstChunk(
            $path,
            FileHelper::FOLDER_SEPARATOR
        );
    }

    public static function trimLastPathChunk(
        string $path
    ): string {
        return TextHelper::trimLastChunk(
            $path,
            FileHelper::FOLDER_SEPARATOR
        );
    }

    public static function joinPathParts(array $parts): string
    {
        return implode(
            self::FOLDER_SEPARATOR,
            $parts
        );
    }
}
