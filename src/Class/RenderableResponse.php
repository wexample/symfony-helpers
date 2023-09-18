<?php

namespace Wexample\SymfonyHelpers\Class;

use Exception;
use Wexample\SymfonyHelpers\Class\ResponseRenderProcessor\AbstractResponseRenderProcessor;
use Wexample\SymfonyHelpers\Class\ResponseRenderProcessor\CliResponseRenderProcessor;
use Wexample\SymfonyHelpers\Class\ResponseRenderProcessor\JsonResponseRenderProcessor;
use Wexample\SymfonyHelpers\Class\ResponseRenderProcessor\YamlResponseRenderProcessor;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

/**
 * Two key concepts for this class :
 * - Output type : is the destination of the data : cli, api, file, database, etc...
 * - Output format : the conversion format : json, yaml, etc...
 *
 * The user will define the output type, and the format will be detecting regarding it.
 * The prepareRender() method will let the child class to adjust the data regarding the output type
 * (e.g. adding some information for the api response).
 * A ResponseRenderProcess will be used to convert data to chosen format.
 *
 * Note that CLI has both output type and output format as it have a special processing behavior.
 */
class RenderableResponse
{
    final public const OUTPUT_TYPE_API = VariableHelper::API;

    final public const OUTPUT_TYPE_CLI = VariableHelper::CLI;

    final public const OUTPUT_TYPE_DEFAULT = VariableHelper::DEFAULT;

    final public const OUTPUT_FORMAT_CLI = VariableHelper::CLI;

    final public const OUTPUT_FORMAT_JSON = VariableHelper::JSON;

    final public const OUTPUT_FORMAT_YAML = VariableHelper::YAML;

    protected array $data = [];

    protected string $outputType;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->setOutputType(
            $this->getDefaultOutputType()
        );
    }

    protected function getResponseProcessors(): array
    {
        return [
            self::OUTPUT_FORMAT_CLI => CliResponseRenderProcessor::class,
            self::OUTPUT_FORMAT_JSON => JsonResponseRenderProcessor::class,
            self::OUTPUT_FORMAT_YAML => YamlResponseRenderProcessor::class,
        ];
    }

    public function mapOutputFormats(): array
    {
        return [
            self::OUTPUT_TYPE_API => self::OUTPUT_FORMAT_JSON,
            self::OUTPUT_TYPE_CLI => self::OUTPUT_FORMAT_CLI,
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

    public function prepareRender(): array
    {
        return $this->data;
    }

    /**
     * @throws Exception
     */
    public function render(): string
    {
        if (!$format = $this->mapOutputFormats()[$this->outputType] ?? null) {
            throw new Exception('Unable to find output format for type '.$this->outputType);
        }

        if (!$processorType = $this->getResponseProcessors()[$format] ?? null) {
            throw new Exception('Unable to find output processor for format '.$format);
        }

        /** @var AbstractResponseRenderProcessor $processor */
        $processor = new $processorType();
        $renderData = $this->prepareRender();

        return $processor->renderResponseData(
            $renderData,
            $this
        );
    }

    /**
     * @throws Exception
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
