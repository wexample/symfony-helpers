<?php

namespace Wexample\SymfonyHelpers\Tests\Class;

use Wexample\SymfonyHelpers\Tests\Class\Traits\LoggingTestCaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wexample\SymfonyHelpers\Helper\TextHelper;

abstract class AbstractWebTestCase extends WebTestCase
{
    use LoggingTestCaseTrait;

    /**
     * Generates an url from route.
     */
    abstract public function url($route, array $args = []): string;

    protected function setUp(): void
    {
        parent::setUp();

        $this->log(
            PHP_EOL . '____ TESTING : ' . static::class,
            TextHelper::ASCII_COLOR_CYAN
        );
    }

    /**
     * Return the root path of the website.
     */
    abstract public function getStorageDir(string $name = null): string;
}
