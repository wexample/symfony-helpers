<?php

namespace Wexample\SymfonyHelpers\Class;

use Exception;
use Wexample\SymfonyHelpers\Class\ResponseRenderProcessor\AbstractResponseRenderProcessor;
use Wexample\SymfonyHelpers\Class\ResponseRenderProcessor\JsonResponseRenderProcessor;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

class RenderableResponse
{
    final public const OUTPUT_TYPE_DEFAULT = VariableHelper::DEFAULT;

    final public const OUTPUT_FORMAT_JSON = VariableHelper::JSON;

    protected array $data = [];
    private string $outputType;

    public function __construct()
    {
        $this->setOutputType(
            $this->getDefaultOutputType()
        );
    }

    public function mapOutputFormats(): array
    {
        return [
            self::OUTPUT_TYPE_DEFAULT => self::OUTPUT_FORMAT_JSON,
        ];
    }

    /**
     * @throws Exception
     */
    public function setOutputType(string $outputType): void
    {
        if (!in_array($outputType, $this->getOutputsTypes())) {
            throw new Exception('Unable to set undefined output type '.$outputType);
        }

        $this->outputType = $outputType;
    }

    public function getDefaultOutputType(): string
    {
        return self::OUTPUT_TYPE_DEFAULT;
    }

    public function getOutputsTypes(): array
    {
        return [
            self::OUTPUT_TYPE_DEFAULT,
        ];
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function get(
        string $key,
    ): mixed {
        return $this->data[$key];
    }

    public function set(
        string $key,
        mixed $value
    ): array {
        $this->data[$key] = $value;

        return $this->data;
    }

    public function merge(array $data): array
    {
        $this->data = array_merge(
            $this->data,
            $data
        );

        return $this->data;
    }

    public function prepareRender(string $format): array
    {
        return $this->data;
    }

    protected function getResponseProcessors(): array
    {
        return [
            self::OUTPUT_FORMAT_JSON => JsonResponseRenderProcessor::class,
        ];
    }

    public function render(): string
    {
        if (!$format = $this->mapOutputFormats()[$this->outputType] ?? null) {
            throw new Exception('Unable to find output format for type '.$this->outputType);
        }

        /** @var AbstractResponseRenderProcessor $processor */
        if (!$processor = $this->getResponseProcessors()[$format] ?? null) {
            throw new Exception('Unable to find output processor for format '.$format);
        }

        $renderData = $this->prepareRender($format);

        return $processor->renderResponseData(
            $renderData,
            $this
        );
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
