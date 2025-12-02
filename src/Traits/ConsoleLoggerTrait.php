<?php

namespace Wexample\SymfonyHelpers\Traits;

use Wexample\Helpers\Helper\ClassHelper;
use Wexample\Helpers\Helper\TextHelper;
use Wexample\SymfonyHelpers\Entity\Interfaces\AbstractEntityInterface;

trait ConsoleLoggerTrait
{
    public static function getAllLogColors(): array
    {
        return [
            TextHelper::ASCII_BG_COLOR_BLUE,
            TextHelper::ASCII_COLOR_GRAY,
            TextHelper::ASCII_COLOR_BLUE,
            TextHelper::ASCII_COLOR_CYAN,
            TextHelper::ASCII_COLOR_GREEN,
            TextHelper::ASCII_COLOR_MAGENTA,
            TextHelper::ASCII_COLOR_RED,
            TextHelper::ASCII_COLOR_WHITE,
            TextHelper::ASCII_COLOR_YELLOW,
            TextHelper::ASCII_DARK_COLOR_GRAY,
        ];
    }

    public int $logIndentCursor = 0;

    public function logTitle(
        string $message,
    ): void {
        $this->log(
            PHP_EOL.'# '.strtoupper($message),
            TextHelper::ASCII_COLOR_CYAN,
            0
        );
    }

    abstract public function log(
        string $message,
        string $color = TextHelper::ASCII_COLOR_WHITE,
        int $indent = null
    ): void;

    public function logEntity(
        AbstractEntityInterface $abstractEntity,
        string $message,
        string $color = null,
        int $indent = null
    ): void {
        $this->log(
            $this->buildLocalEntityName($abstractEntity).' | '.$message,
            $color,
            $indent
        );
    }

    public function buildLocalEntityName(AbstractEntityInterface $entity): string
    {
        return ClassHelper::getTableizedName($entity).' #'.$entity->getId();
    }

    public function logSuccessCheckbox(
        string $message,
        int $indent = null
    ): void {
        $this->logSuccess(
            '✓ '.$message,
            $indent
        );
    }

    public function logSuccess(
        string $message,
        int $indent = null
    ): void {
        $this->log(
            $message,
            TextHelper::ASCII_COLOR_GREEN,
            $indent
        );
    }

    public function logWarn(
        string $message,
        int $indent = null
    ): void {
        $this->log(
            $message,
            TextHelper::ASCII_COLOR_YELLOW,
            $indent
        );
    }

    public function logErrorCheckbox(
        string $message,
        int $indent = null
    ): void {
        $this->logError(
            'x '.$message,
            $indent
        );
    }

    public function logError(
        string $message,
        int $indent = null
    ): void {
        $this->log(
            $message,
            TextHelper::ASCII_COLOR_RED,
            $indent
        );
    }

    public function logIndentUp(): int
    {
        return ++$this->logIndentCursor;
    }

    public function logIndentDown(): int
    {
        return --$this->logIndentCursor;
    }

    public function logIndentReset(): void
    {
        $this->logIndentCursor = 0;
    }

    public function formatLogMessage(
        array|object|string|null $message,
        string $color = TextHelper::ASCII_DARK_COLOR_GRAY,
        int $indent = null
    ): string {
        if (! is_string($message)) {
            $message = json_encode($message);
        }

        return str_repeat(
            '  ',
            is_null($indent) ? $this->logIndentCursor : $indent
        ).
            ($color ? TextHelper::asciiColorWrap($message, $color) : $message);
    }
}
