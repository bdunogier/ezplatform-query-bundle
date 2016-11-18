<?php

namespace BD\EzPlatformQueryBundle;

use BD\EzPlatformQueryBundle\DependencyInjection\Compiler\QueryTypesListPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BDEzPlatformQueryBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new QueryTypesListPass());
    }
}
