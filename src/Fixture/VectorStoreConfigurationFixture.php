<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Guiziweb\SyliusAIPlatformBundle\Fixture\Factory\VectorStoreConfigurationExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sylius_fixtures.fixture')]
final class VectorStoreConfigurationFixture extends AbstractResourceFixture
{
    public function __construct(
        EntityManagerInterface $entityManager,
        VectorStoreConfigurationExampleFactory $exampleFactory,
    ) {
        parent::__construct($entityManager, $exampleFactory);
    }

    public function getName(): string
    {
        return 'vector_store_configuration';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('channel')->cannotBeEmpty()->end()
                ->scalarNode('platform_configuration')->cannotBeEmpty()->end()
                ->scalarNode('model')->cannotBeEmpty()->end()
                ->scalarNode('distance_metric')->end()
        ;
    }
}