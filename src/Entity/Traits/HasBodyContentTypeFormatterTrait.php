<?php

namespace Wexample\SymfonyHelpers\Entity\Traits;

use Wexample\SymfonyHelpers\Helper\DataHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

trait HasBodyContentTypeFormatterTrait
{
    use HasBodyTrait;
    use HasContentTypeTrait {
        getContentType as _getContentType;
    }

    public const string BODY_FORMAT_JSON = DataHelper::FORMAT_JSON;
    public const string BODY_FORMAT_SERIALIZED = 'serialized';
    public const string BODY_FORMAT_TEXT = VariableHelper::VARIABLE_TYPE_TEXT;

    public function getContentType(): string
    {
        return $this->_getContentType() ?: self::BODY_FORMAT_TEXT;
    }

    public function getBodyAllowedFormats(): array
    {
        return [
            self::BODY_FORMAT_JSON,
            self::BODY_FORMAT_SERIALIZED,
            self::BODY_FORMAT_TEXT,
        ];
    }

    public function setBodyFromFormatted(mixed $formatted): HasBodyContentTypeFormatterTrait
    {
        $body = $formatted;

        switch ($this->getContentType()) {
            case self::BODY_FORMAT_JSON:
                $body = json_encode($body);
            case self::BODY_FORMAT_SERIALIZED:
                $body = serialize($body);
        }

        return $this->setBody($body);
    }

    public function getFormattedBody(): mixed
    {
        $body = $this->getBody();

        return match ($this->getContentType()) {
            self::BODY_FORMAT_JSON => json_decode($body),
            self::BODY_FORMAT_SERIALIZED => unserialize($body),
            default => $body,
        };
    }
}
