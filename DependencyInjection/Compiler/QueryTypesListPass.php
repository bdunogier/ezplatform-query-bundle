<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace BD\EzPlatformQueryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Creates a parameter that lists the names of the registered QueryTypes.
 */
class QueryTypesListPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezpublish.query_type.registry')) {
            return;
        }

        $queryTypes = [];

        $queryTypeRegistryDefinition = $container->getDefinition('ezpublish.query_type.registry');
        foreach ($queryTypeRegistryDefinition->getMethodCalls() as $methodCall) {
            if ($methodCall[0] === 'addQueryType') {
                if (!$methodCall[1][1] instanceof Reference) {
                    continue;
                }
                $queryTypes[$methodCall[1][0]] = (string)$methodCall[1][1];
            } elseif ($methodCall[0] == 'addQueryTypes') {
                foreach ($methodCall[1][0] as $queryTypeName => $value) {
                    if (!$value instanceof Reference) {
                        continue;
                    }
                    $queryTypes[$queryTypeName] = (string)$value;
                }
            }
        }

        $container->setParameter('bd_query.query_types', $queryTypes);
    }
}
