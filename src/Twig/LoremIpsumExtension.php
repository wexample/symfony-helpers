<?php

namespace Wexample\SymfonyHelpers\Twig;

use Twig\TwigFunction;
use Wexample\Helpers\Helper\LoremIpsumHelper;

class LoremIpsumExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'lorem_ipsum',
                [$this, 'loremIpsum']
            ),
        ];
    }

    public function loremIpsum(int $length): string
    {
        return LoremIpsumHelper::generate($length);
    }
}
