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

class ListQueryTypesCommand extends Command
{
    /**
     * @var string[]
     */
    private $queryTypes;

    /**
     * @var QueryTypeRegistry
     */
    private $queryTypeRegistry;

    public function __construct($queryTypes, QueryTypeRegistry $queryTypeRegistry)
    {
        parent::__construct();

        ksort($queryTypes);
        $this->queryTypes = $queryTypes;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    protected function configure()
    {
        $this
            ->setName('bd:query:types')
            ->setDescription('Lists the registered QueryTypes.')
            ->addArgument('name', InputArgument::OPTIONAL, 'If set, shows details about the QueryType with that name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('name')) {
            $queryTypeName = $input->getArgument('name');
            $queryType = $this->queryTypeRegistry->getQueryType($queryTypeName);

            $output->writeln(
                sprintf(
                    <<<'OUT'
Class: "%s"
Service: "%s",
Parameters: %s
OUT
                    ,
                    get_class($queryType),
                    $this->queryTypes[$queryTypeName],
                    implode(', ', $queryType->getSupportedParameters())
                )
            );

        } else {
            $output->writeln(sprintf('There are %d registered QueryTypes:', count($this->queryTypes)));
            $output->writeln('');

            array_map(
                function ($name) use ($output) {
                    $output->writeln('- ' . $name);
                },
                array_keys($this->queryTypes)
            );
        }
    }
}
