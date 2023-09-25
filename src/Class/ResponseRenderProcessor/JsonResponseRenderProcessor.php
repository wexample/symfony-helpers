<?php

namespace Wexample\SymfonyHelpers\Class\ResponseRenderProcessor;

use Wexample\SymfonyHelpers\Class\RenderableResponse;

class JsonResponseRenderProcessor extends AbstractResponseRenderProcessor
{
    public function renderResponseData(
        array $data,
        RenderableResponse $response
    ): string
    {
        return json_encode(
            $data
        );
    }
}
