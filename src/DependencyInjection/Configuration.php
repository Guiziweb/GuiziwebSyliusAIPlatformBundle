<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('guiziweb_sylius_ai_platform');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
