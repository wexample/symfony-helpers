<?php

namespace Wexample\SymfonyHelpers\Interface;

/**
 * What a bundle adds to a page's head: the Open Graph card of a link shared,
 * a verification of ownership, a hint to a crawler. Any installed bundle may
 * be one; the page's base template prints them all, none of them known to it.
 */
interface HeadMetaProviderInterface
{
    public const string TAG = 'wexample.head_meta_provider';

    /**
     * @param array{title: string, description: ?string, url: ?string} $document what the
     *                                                                            page already says of itself
     *
     * @return list<array<string, string>> the attributes of each `<meta>` tag
     */
    public function getHeadMeta(array $document): array;
}
