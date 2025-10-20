<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Guiziweb\SyliusAIPlatformBundle\Fixture\Factory\AgentConfigurationExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sylius_fixtures.fixture')]
final class AgentConfigurationFixture extends AbstractResourceFixture
{
    public function __construct(
        EntityManagerInterface $entityManager,
        AgentConfigurationExampleFactory $exampleFactory,
    ) {
        parent::__construct($entityManager, $exampleFactory);
    }

    public function getName(): string
    {
        return 'agent_configuration';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('channel')->cannotBeEmpty()->end()
                ->scalarNode('platform')->cannotBeEmpty()->end()
                ->scalarNode('model')->cannotBeEmpty()->end()
                ->scalarNode('system_prompt')->end()
                ->arrayNode('tools')->scalarPrototype()->end()->end()
        ;
    }
}