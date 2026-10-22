<?php

namespace Wexample\SymfonyHelpers\Interface;

/**
 * What a bundle links from a page's head: a file the page will need, asked
 * for before the stylesheet naming it has even arrived. Any installed bundle
 * may be one; the page's base template prints them all, ahead of its assets.
 */
interface HeadLinkProviderInterface
{
    public const string TAG = 'wexample.head_link_provider';

    /**
     * @return list<array<string, string>> the attributes of each `<link>` tag
     */
    public function getHeadLinks(): array;
}
