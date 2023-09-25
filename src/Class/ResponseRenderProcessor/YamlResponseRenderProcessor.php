<?php

namespace Wexample\SymfonyHelpers\Class\ResponseRenderProcessor;

use Symfony\Component\Yaml\Yaml; 
use Wexample\SymfonyHelpers\Class\RenderableResponse;

class YamlResponseRenderProcessor extends AbstractResponseRenderProcessor
{
    public function renderResponseData(
        array $data,
        RenderableResponse $response
    ): string
    {
        return Yaml::dump($data);
    }
}
