<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace BD\EzPlatformQueryBundle\Formatter\SearchResult;

use BD\EzPlatformQueryBundle\Formatter\SearchHit\SearchHitFormatter;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A basic formatter that outputs the list of results to an OutputInterface.
 */
class SimpleOutputResultFormatter implements SearchResultFormatter
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var SearchHitFormatter
     */
    private $searchHitFormatter;

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function __construct(SearchHitFormatter $searchHitFormatter)
    {
        $this->searchHitFormatter = $searchHitFormatter;
    }

    function format(SearchResult $searchResult)
    {
        if (!isset($this->output)) {
            throw new \RuntimeException("An OutputInterface is required.");
        }

        $this->output->writeln(
            sprintf(
                "%d result(s) found in %0.002f seconds:",
                $searchResult->totalCount,
                $searchResult->time
            )
        );
        $this->output->writeln('');

        foreach ($searchResult->searchHits as $searchHit) {

            $this->output->writeln($this->searchHitFormatter->format($searchHit));

        }
    }
}
