<?php

namespace Wexample\SymfonyHelpers\Class\ResponseRenderProcessor;

use Wexample\SymfonyHelpers\Class\RenderableResponse;
use Wexample\SymfonyHelpers\Helper\TextHelper;

class ListResponseRenderProcessor extends AbstractResponseRenderProcessor
{
    protected function getJoinSeparator(): string
    {
        return PHP_EOL;
    }

    protected function convertFirstLevelValue(
        mixed $value,
        string|int $key
    ): string {
        return TextHelper::toString($value);
    }

    protected function flattenList(array $list): array
    {
        $output = [];
        foreach ($list as $key => $value) {
            $output[$key] = $this->convertFirstLevelValue($value, $key);
        }

        return $output;
    }

    public function renderResponseData(
        array $data,
        RenderableResponse $response
    ): string {
        return implode(
            $this->getJoinSeparator(),
            $this->flattenList($data)
        );
    }
}
