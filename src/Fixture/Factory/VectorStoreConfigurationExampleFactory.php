<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Fixture\Factory;

use Guiziweb\SyliusAIPlatformBundle\Entity\VectorStoreConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Repository\PlatformConfigurationRepositoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VectorStoreConfigurationExampleFactory implements ExampleFactoryInterface
{
    private OptionsResolver $optionsResolver;

    public function __construct(
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly PlatformConfigurationRepositoryInterface $platformConfigurationRepository,
    ) {
        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): VectorStoreConfiguration
    {
        $options = $this->optionsResolver->resolve($options);

        $vectorStoreConfiguration = new VectorStoreConfiguration();
        $vectorStoreConfiguration->setCode($options['code']);
        $vectorStoreConfiguration->setName($options['name']);
        $vectorStoreConfiguration->setEnabled($options['enabled']);
        $vectorStoreConfiguration->setModel($options['model']);

        if (null !== $options['distance_metric']) {
            $vectorStoreConfiguration->setDistanceMetric($options['distance_metric']);
        }

        // Resolve channel
        $channel = $this->channelRepository->findOneBy(['code' => $options['channel']]);
        if (null === $channel) {
            throw new \InvalidArgumentException(sprintf('Channel with code "%s" not found', $options['channel']));
        }
        $vectorStoreConfiguration->setChannel($channel);

        // Resolve platform configuration
        $platformConfiguration = $this->platformConfigurationRepository->findOneBy(['code' => $options['platform_configuration']]);
        if (null === $platformConfiguration) {
            throw new \InvalidArgumentException(sprintf('Platform configuration with code "%s" not found', $options['platform_configuration']));
        }
        $vectorStoreConfiguration->setPlatformConfiguration($platformConfiguration);

        return $vectorStoreConfiguration;
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

            ->setRequired('platform_configuration')
            ->setAllowedTypes('platform_configuration', 'string')

            ->setRequired('model')
            ->setAllowedTypes('model', 'string')

            ->setDefault('distance_metric', 'cosine')
            ->setAllowedTypes('distance_metric', ['null', 'string'])
        ;
    }
}