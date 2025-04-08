<?php

namespace Wexample\SymfonyHelpers\Exception;

/**
 * Base exception class for all application exceptions.
 *
 * This class provides a standardized way to handle errors across all applications
 * with support for error codes, context data, and formatted messages.
 */
abstract class AbstractException extends \Exception
{
    /**
     * Error context data that provides additional information about the error.
     *
     * @var array
     */
    protected array $context = [];

    /**
     * String error code for human-readable identification across projects.
     * Format example: "C-N-021" where letters can identify the source/project.
     *
     * @var string|null
     */
    protected ?string $internalCode = null;

    /**
     * Creates a new base exception.
     *
     * @param string $message The error message
     * @param int $code The numeric error code (for HTTP/system compatibility)
     * @param string|null $internalCode The string error code (for cross-project identification)
     * @param array $context Additional context data related to the error
     * @param \Throwable|null $previous The previous exception if nested
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?string $internalCode = null,
        array $context = [],
        \Throwable $previous = null
    )
    {
        $this->context = $context;
        $this->internalCode = $internalCode;

        parent::__construct($message, $code, $previous);
    }

    abstract public function getInternalCodeParts(): array;

    function buildInternalCode(): string
    {
        return implode('-', $this->getInternalCodeParts());
    }

    /**
     * Gets the string error code.
     *
     * @return string|null The string error code
     */
    public function getInternalCode(): ?string
    {
        return $this->internalCode;
    }

    /**
     * Gets the error context data.
     *
     * @return array The error context
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
