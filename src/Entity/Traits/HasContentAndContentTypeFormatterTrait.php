<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Wexample\SymfonyHelpers\Helper\DataHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasContentAndContentTypeFormatterTrait
{
    use HasContentTrait;
    use HasContentTypeTrait {
        getContentType as _getContentType;
    }

    public const string FORMAT_JSON = DataHelper::FORMAT_JSON;
    public const string FORMAT_SERIALIZED = 'serialized';
    public const string FORMAT_TEXT = VariableHelper::VARIABLE_TYPE_TEXT;

    public function getContentType(): string
    {
        return $this->_getContentType() ?: self::FORMAT_TEXT;
    }

    public function getContentAllowedFormats(): array
    {
        return [
            self::FORMAT_JSON,
            self::FORMAT_SERIALIZED,
            self::FORMAT_TEXT,
        ];
    }

    public function setContentFromFormatted(mixed $formatted): HasContentAndContentTypeFormatterTrait
    {
        $content = $formatted;

        switch ($this->getContentType()) {
            case self::FORMAT_JSON:
                $content = json_encode($content);
            case self::FORMAT_SERIALIZED:
                $content = serialize($content);
        }

        return $this->setContent($content);
    }

    public function getFormattedContent(): mixed
    {
        $content = $this->getContent();

        return match ($this->getContentType()) {
            self::FORMAT_JSON => json_decode($content),
            self::FORMAT_SERIALIZED => unserialize($content),
            default => $content,
        };
    }
}
