<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace BD\EzPlatformQueryBundle\Formatter\SearchResult;

use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * Formats a SearchResult into a string.
 */
interface SearchResultFormatter
{
    /**
     * @return string
     */
    public function format(SearchResult $searchResult);
}
