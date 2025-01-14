<?php

namespace Wexample\SymfonyHelpers\Helper;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function is_dir;

class FileHelper
{
    final public const PATTERN_GLOB = 'glob';

    final public const PATH_VAR = 'var/';

    final public const EXTENSION_SEPARATOR = '.';
    final public const FOLDER_SEPARATOR = '/';

    final public const ENCODING_UTF8 = 'UTF-8';
    final public const ENCODING_ISO_8859_1 = 'ISO-8859-1';
    final public const ENCODING_ISO_8859_15 = 'ISO-8859-15';
    final public const ENCODING_WINDOWS_1252 = 'Windows-1252';
    final public const ENCODING_ASCII = 'ASCII';

    final public const FILE_EXTENSION_CSV = 'csv';
    final public const FILE_EXTENSION_PDF = 'pdf';
    final public const FILE_EXTENSION_PHP = 'php';
    final public const FILE_EXTENSION_YML = 'yml';
    final public const FILE_EXTENSION_VUE = 'vue';
    final public const FILE_EXTENSION_SVG = 'svg';
    final public const FILE_EXTENSION_TXT = 'txt';
    final public const FILE_EXTENSION_XLSX = 'xlsx';

    final public const SUFFIX_AGGREGATED = 'agg';

    /**
     * Common MIME types.
     */

    // Text files
    final public const MIME_TEXT_PLAIN = 'text/plain';
    final public const MIME_TEXT_HTML = 'text/html';
    final public const MIME_TEXT_CSS = 'text/css';
    final public const MIME_TEXT_JAVASCRIPT = 'application/javascript';

    // Images
    final public const MIME_IMAGE_JPEG = 'image/jpeg';
    final public const MIME_IMAGE_PNG = 'image/png';
    final public const MIME_IMAGE_GIF = 'image/gif';
    final public const MIME_IMAGE_SVG = 'image/svg+xml';
    final public const MIME_IMAGE_WEBP = 'image/webp';

    // Audio
    final public const MIME_AUDIO_MP3 = 'audio/mpeg';
    final public const MIME_AUDIO_OGG = 'audio/ogg';
    final public const MIME_AUDIO_WAV = 'audio/wav';

    // Video
    final public const MIME_VIDEO_MP4 = 'video/mp4';
    final public const MIME_VIDEO_WEBM = 'video/webm';
    final public const MIME_VIDEO_OGG = 'video/ogg';

    // Application files
    final public const MIME_APPLICATION_PDF = 'application/pdf';
    final public const MIME_APPLICATION_ZIP = 'application/zip';
    final public const MIME_APPLICATION_RAR = 'application/vnd.rar';
    final public const MIME_APPLICATION_JSON = 'application/json';
    final public const MIME_APPLICATION_XML = 'application/xml';
    final public const MIME_APPLICATION_MSWORD = 'application/msword';
    final public const MIME_APPLICATION_EXCEL = 'application/vnd.ms-excel';
    final public const MIME_APPLICATION_POWERPOINT = 'application/vnd.ms-powerpoint';
    final public const MIME_APPLICATION_OPENXML_WORD = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    final public const MIME_APPLICATION_OPENXML_EXCEL = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    final public const MIME_APPLICATION_OPENXML_POWERPOINT = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';

    // Archives
    final public const MIME_ARCHIVE_TAR = 'application/x-tar';
    final public const MIME_ARCHIVE_GZIP = 'application/gzip';

    // Fonts
    final public const MIME_FONT_WOFF = 'font/woff';
    final public const MIME_FONT_WOFF2 = 'font/woff2';
    final public const MIME_FONT_TTF = 'font/ttf';
    final public const MIME_FONT_OTF = 'font/otf';

    // Others
    final public const MIME_BINARY_OCTET_STREAM = 'application/octet-stream';


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

    public static function removeExtension(
        string $path,
        string $extension = null
    ): string {
        if (is_null($extension)) {
            $extension = pathinfo($path)['extension'];
        }

        // Supports if given extension starts with a dot.
        if ('.' !== $extension[0]) {
            $extension = '.'.$extension;
        }

        return substr($path, 0, -strlen($extension));
    }

    public static function fileWriteAndHash(
        string $fileName,
        string $content
    ): string {
        self::fileWrite($fileName, $content);

        return md5($content);
    }

    public static function fileWrite(
        string $fileName,
        string $content
    ): void {
        $dir = dirname($fileName);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($fileName, $content);
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

    public static function buildRelativePath(
        string $filePath,
        string $relativeTo
    ): ?string {
        if (str_starts_with($filePath, $relativeTo)) {
            $relativePath = substr($filePath, strlen($relativeTo));
            // Ensure the relative path does not start with a '/'
            return ltrim($relativePath, '/');
        } else {
            return null;
        }
    }
}
