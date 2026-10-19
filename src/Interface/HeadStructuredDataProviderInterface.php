<?php

namespace Wexample\SymfonyHelpers\Interface;

/**
 * What a bundle tells the search engines of a page in their own vocabulary,
 * schema.org: who the site is about, what the page is. Any installed bundle may
 * be one; the page's base template prints each object as a JSON-LD block.
 */
interface HeadStructuredDataProviderInterface
{
    public const string TAG = 'wexample.head_structured_data_provider';

    /**
     * @param array{title: string, description: ?string, url: ?string} $document what the
     *                                                                            page already says of itself
     *
     * @return list<array<string, mixed>> one schema.org object per JSON-LD block
     */
    public function getHeadStructuredData(array $document): array;
}
