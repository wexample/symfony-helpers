<?php

namespace Wexample\SymfonyHelpers\Class\ResponseRenderProcessor;

use Wexample\SymfonyHelpers\Class\ArrayToTextTable;
use Wexample\SymfonyHelpers\Helper\ArrayHelper;

class CliResponseRenderProcessor extends KeyValueResponseRenderProcessor
{

    protected function convertFirstLevelValue(
        mixed $value,
        string|int $key
    ): string {
        if (is_array($value)) {

            return PHP_EOL.new ArrayToTextTable(
                    ArrayHelper::toStringTable($value)
                );
        }

        return parent::convertFirstLevelValue(
            $value,
            $key
        );
    }
}
