<?php

namespace Wexample\SymfonyHelpers\Class\ResponseRenderProcessor;

use Wexample\SymfonyHelpers\Class\RenderableResponse;

class ArrayResponseRenderProcessor extends AbstractResponseRenderProcessor
{
    public function renderResponseData(
        array $data,
        RenderableResponse $response
    ): array {
        return $data;
    }
}
