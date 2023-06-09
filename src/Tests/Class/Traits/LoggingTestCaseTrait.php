<?php

namespace Wexample\SymfonyHelpers\Tests\Class\Traits;

use Symfony\Component\DomCrawler\Crawler;
use Wexample\SymfonyHelpers\Helper\DateHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Traits\ConsoleLoggerTrait;

/**
 * Trait LoggingTestCase
 * Various debug and logging helper methods.
 */
trait LoggingTestCaseTrait
{
    use ConsoleLoggerTrait;

    public function log(
        string|array|object|null $message,
        string $color = TextHelper::ASCII_COLOR_WHITE,
        int $indent = null
    ): void {
        \fwrite(
            STDERR,
            PHP_EOL.$this->formatLogMessage(
                $message,
                $color,
                $indent
            )
        );
    }

    public function logSecondary(
        string|array|object $message,
        int $indent = null
    ): void {
        $this->log(
            $message,
            TextHelper::ASCII_DARK_COLOR_GRAY,
            $indent ?: $this->logIndentCursor + 1,
        );
    }

    public function logArray($array): void
    {
        $this->log(
            \print_r(
                $array,
                true
            )
        );
    }

    public function error(string $message, bool $fatal = true): void
    {
        $this->log(
            $message,
            31
        );
        if ($fatal) {
            $this->fail($message);
        }
    }

    public function debugWrite(
        $body = null,
        $fileName = 'phpunit.debug.html',
        $quiet = false
    ): void {
        $tmpDir = $this->initTempDir();

        $logFile = $tmpDir.$fileName;

        if (\is_file($logFile)) {
            \unlink($logFile);
        }

        $output = $body ?: $this->content()
                // Error pages contains svg which breaks readability.
                .'<style> svg { display:none; } </style>';

        \file_put_contents(
            $logFile,
            'At '
            .(new \DateTime())->format(DateHelper::DATE_PATTERN_TIME_DEFAULT)
            .'<br><br>'
            .$output
        );

        if (!$quiet) {
            $this->info('See : '.$logFile);
            $this->logIfErrorPage();
        }
    }

    public function logIfErrorPage(): void
    {
        $crawler = new Crawler($this->content());
        $nodeList = $crawler->filter('h1.exception-message');

        if ($nodeList->count()) {
            $errorMessage = $nodeList->text();
            $this->error($errorMessage, false);
        }
    }

    public function initTempDir(): string
    {
        $tmpDir = $this->getStorageDir('tmp');

        if (!\is_dir($tmpDir)) {
            \mkdir($tmpDir, 0777, true);
        }

        return $tmpDir;
    }

    public function info(string $message): void
    {
        $this->log(
            $message,
            34
        );
    }
}
