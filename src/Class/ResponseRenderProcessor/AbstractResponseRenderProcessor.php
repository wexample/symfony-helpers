<?php

namespace Wexample\SymfonyHelpers\Class\ResponseRenderProcessor;

use Wexample\SymfonyHelpers\Class\RenderableResponse;

abstract class AbstractResponseRenderProcessor
{
    abstract public function renderResponseData(
        array $data,
        RenderableResponse $response
    ): array|string;
}
