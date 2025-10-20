<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Fixture\Factory;

use Guiziweb\SyliusAIPlatformBundle\Entity\AgentConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Entity\AgentTool;
use Guiziweb\SyliusAIPlatformBundle\Repository\PlatformConfigurationRepositoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AgentConfigurationExampleFactory implements ExampleFactoryInterface
{
    private OptionsResolver $optionsResolver;

    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly PlatformConfigurationRepositoryInterface $platformConfigurationRepository,
    ) {
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): AgentConfiguration
    {
        $options = $this->optionsResolver->resolve($options);

        $agentConfiguration = new AgentConfiguration();
        $agentConfiguration->setCode($options['code']);
        $agentConfiguration->setName($options['name']);
        $agentConfiguration->setEnabled($options['enabled']);
        $agentConfiguration->setModel($options['model']);

        if (null !== $options['system_prompt']) {
            $agentConfiguration->setSystemPrompt($options['system_prompt']);
        }

        // Set channel
        $channel = $this->channelRepository->findOneBy(['code' => $options['channel']]);
        \assert($channel instanceof ChannelInterface);
        $agentConfiguration->setChannel($channel);

        // Set platform configuration
        $platformConfiguration = $this->platformConfigurationRepository->findOneBy(['code' => $options['platform']]);
        if (null !== $platformConfiguration) {
            $agentConfiguration->setPlatformConfiguration($platformConfiguration);
        }

        // Create agent tools
        if (!empty($options['tools'])) {
            foreach ($options['tools'] as $toolName) {
                $agentTool = new AgentTool();
                $agentTool->setToolName($toolName);
                $agentTool->setEnabled(true);
                $agentConfiguration->addAgentTool($agentTool);
            }
        }

        return $agentConfiguration;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('code')
            ->setAllowedTypes('code', 'string')

            ->setRequired('name')
            ->setAllowedTypes('name', 'string')

            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')

            ->setRequired('channel')
            ->setAllowedTypes('channel', 'string')

            ->setRequired('platform')
            ->setAllowedTypes('platform', 'string')

            ->setRequired('model')
            ->setAllowedTypes('model', 'string')

            ->setDefault('system_prompt', null)
            ->setAllowedTypes('system_prompt', ['null', 'string'])

            ->setDefault('tools', [])
            ->setAllowedTypes('tools', 'array')
        ;
    }
}