<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace BD\EzPlatformQueryBundle\Command;

use BD\EzPlatformQueryBundle\Exception\InvalidTypeException;
use BD\EzPlatformQueryBundle\Formatter\SearchHit\SearchHitFormatter;
use BD\EzPlatformQueryBundle\Formatter\SearchResult\SearchResultFormatter;
use BD\EzPlatformQueryBundle\Formatter\SearchResult\SimpleOutputResultFormatter;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunQueryCommand extends Command
{
    /**
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @var \eZ\Publish\API\Repository\SearchService
     */
    private $searchService;

    /**
     * @var \BD\EzPlatformQueryBundle\Formatter\SearchResult\SearchResultFormatter
     */
    private $formatter;

    public function __construct(SearchService $searchService, QueryTypeRegistry $queryTypeRegistry, SearchResultFormatter $formatter)
    {
        parent::__construct();

        $this->searchService = $searchService;
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->formatter = $formatter;
    }

    protected function configure()
    {
        $this
            ->setName('bd:query:run')
            ->setDescription('Runs a Query, based on a QueryType from the repository, and formats the result.')
            ->addOption('search_type', 't', InputOption::VALUE_REQUIRED, "The type of search to run: locations, content or content_info. If not specified, will be guessed.", null)
            ->addArgument('query_type_name', InputArgument::REQUIRED, "The name of a QueryType.")
            ->addArgument('parameters', InputArgument::IS_ARRAY, 'Query type parameters, as name:value. Use multiple times to provide different arguments.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queryType = $this->queryTypeRegistry->getQueryType($input->getArgument('query_type_name'));
        $parameters = $this->getParameters($input, $output);
        $this->checkParameters($output, $parameters, $queryType);
        $query = $queryType->getQuery($parameters);

        $method = $this->guessMethod($query, $input->getOption('search_type'));

        $this->formatter->setOutput($output);
        $this->formatter->format($this->searchService->$method($query));
    }

    /**
     * Extracts the QueryType parameters from the input arguments.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array hash of parameter name / parameter value
     */
    protected function getParameters(InputInterface $input, OutputInterface $output)
    {
        $parameters = [];
        foreach ($input->getArgument('parameters') as $parameter) {
            if (strstr($parameter, ':') === false) {
                $output->writeln("Invalid parameter $parameter");
            }
            list($name, $value) = explode(':', $parameter);
            $parameters[$name] = $value;
        }

        return $parameters;
    }

    /**
     * @param OutputInterface $output
     * @param $parameters
     * @param $queryType
     */
    protected function checkParameters(OutputInterface $output, $parameters, $queryType)
    {
        $unsupportedParameters = array_diff(array_keys($parameters), $queryType->getSupportedParameters());
        if (count($unsupportedParameters) > 0) {
            $output->writeln(
                sprintf(
                    "The following parameters are not supported by this query type: %s. Supported parameters: %s.",
                    implode(',', $unsupportedParameters),
                    implode(',', $queryType->getSupportedParameters())
                )
            );
        }
    }

    public function setQueryTypeRegistry(QueryTypeRegistry $queryTypeRegistry)
    {
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    public function setSearchService(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Guesses the SearchService method to use based on the Query and the search-type option.
     * @param Query $query
     * @param string $option
     * @return mixed
     */
    private function guessMethod(Query $query, $option)
    {
        if ($option === null) {
            $typeMethodMap = [
                'eZ\Publish\API\Repository\Values\Content\Query' => 'findContentInfo',
                'eZ\Publish\API\Repository\Values\Content\LocationQuery' => 'findLocations',
            ];
            $type = get_class($query);
            if (!isset($typeMethodMap[$type])) {
                throw new InvalidTypeException("Query", $query, 'One of ' . join(array_keys($typeMethodMap)));
            }
            return $typeMethodMap[$type];
        }

        $optionMethodMap = [
            'locations' => 'findLocations',
            'content_info' => 'findContentInfo',
            'content' => 'findContent'
        ];
        if (!isset($optionMethodMap[$option])) {
            throw new InvalidTypeException("Query", $query, 'One of ' . join(array_keys($optionMethodMap)));
        }
        return $optionMethodMap[$option];
    }
}
