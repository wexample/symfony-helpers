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
     * The serializer format an object is described in for the engines: what an
     * entity carrying #[SchemaOrg] becomes, and what the API's normalizers leave
     * to it.
     */
    public const string FORMAT_SCHEMA_ORG = 'schema_org';

    /**
     * @param array{title: string, description: ?string, url: ?string, subject: ?object} $document what
     *                                                                                             the page already says of itself, and the entity it is about, if any
     *
     * @return list<array<string, mixed>> one schema.org object per JSON-LD block
     */
    public function getHeadStructuredData(array $document): array;
}
