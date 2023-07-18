<?php

namespace Wexample\SymfonyHelpers\Tests\Class\Traits;

use DOMElement;
use DOMNode;
use Symfony\Component\DomCrawler\Crawler;

trait HtmlDocumentTestCaseTrait
{
    use ApplicationTestCaseTrait;

    /**
     * @return DOMElement|null
     */
    public function findOne(
        string $selector,
        int $index = 0
    ): ?DOMNode {
        $nodes = $this->find($selector);

        return $nodes->getNode($index);
    }

    public function clickOn(string $selector): ?Crawler
    {
        $this->log('Clicking on : '.$selector);

        $link = $this->getCurrentCrawler()->filter($selector);

        if (!$link->count()) {
            $this->error('Link not found for selector : '.$selector);
        }

        $this->client->click($link->link());

        return $this->getCurrentCrawler();
    }

    public function assertPageBodyHasNotOrphanTranslationKey(
        string $body = null,
        Crawler $crawler = null
    ): void {
        $translationKeyPattern = '([\n\t\s]*[a-zA-Z0-9_\.]+::[a-zA-Z0-9_\.]+[\n\t\s]*)';
        $this->logIndentUp();

        $this->assertPageBodyHasNotOrphanTranslationKeyPattern(
            '/>'.$translationKeyPattern.'</',
            'No orphan translation inside html tags',
            $body,
            $crawler,
        );

        $this->assertPageBodyHasNotOrphanTranslationKeyPattern(
            '/\="'.$translationKeyPattern.'"/',
            'No orphan translation inside html attributes',
            $body,
            $crawler,
        );

        $this->logIndentDown();
    }

    public function getBody(Crawler $crawler = null): string
    {
        $crawler ??= $this->getCurrentCrawler();
        $body = $crawler->filter('body');

        if ($body->count()) {
            return $body->html();
        }

        return $this->content();
    }

    private function assertPageBodyHasNotOrphanTranslationKeyPattern(
        string $pattern,
        string $message,
        string $body = null,
        Crawler $crawler = null
    ): void {
        $this->logSecondary($message);

        // Search keys in html attributes.
        preg_match_all(
            $pattern,
            $body ?? $this->getBody($crawler),
            $output
        );

        if (!empty($output[1])) {
            $this->logArray($output[1]);
        }

        $this->assertEmpty(
            $output[1],
            $message,
        );
    }

    public function nodeHasClass(
        $node,
        string $className
    ): bool {
        $classes = explode(' ', (string) $node->getAttribute('class'));

        return in_array($className, $classes);
    }

    public function assertNodeExists($selector): void
    {
        $node = $this->find($selector);

        $this->assertTrue(
            $node->count() > 0,
            'There is a node matching '.$selector
        );
    }

    public function find($selector): Crawler
    {
        return $this->getCurrentCrawler()->filter($selector);
    }
}
