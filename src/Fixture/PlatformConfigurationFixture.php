<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Guiziweb\SyliusAIPlatformBundle\Fixture\Factory\PlatformConfigurationExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sylius_fixtures.fixture')]
final class PlatformConfigurationFixture extends AbstractResourceFixture
{
    public function __construct(
        EntityManagerInterface $entityManager,
        PlatformConfigurationExampleFactory $exampleFactory,
    ) {
        parent::__construct($entityManager, $exampleFactory);
    }

    public function getName(): string
    {
        return 'platform_configuration';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('provider')->cannotBeEmpty()->end()
                ->scalarNode('api_key')->cannotBeEmpty()->end()
                ->arrayNode('settings')->variablePrototype()->end()->end()
        ;
    }
}