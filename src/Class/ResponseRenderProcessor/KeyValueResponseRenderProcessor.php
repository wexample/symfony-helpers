<?php

namespace Wexample\SymfonyHelpers\Class\ResponseRenderProcessor;

use Wexample\SymfonyHelpers\Class\RenderableResponse;

class KeyValueResponseRenderProcessor extends ListResponseRenderProcessor
{
    public function renderResponseData(
        array $data,
        RenderableResponse $response
    ): string {

        $output = [];
        $data = $this->flattenList($data);

        // Alter list to get keys.
        foreach ($data as $key => $value) {
            $output[] = $key.': '.$value;
        }

        return parent::renderResponseData(
            $output,
            $response
        );
    }
}
