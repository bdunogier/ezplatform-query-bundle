<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace BD\EzPlatformQueryBundle\Formatter\SearchHit;

use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;

interface SearchHitFormatter
{
    public function format(SearchHit $searchHit);
}
