<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Provider;

use Guiziweb\SyliusAIPlatformBundle\Factory\VectorStoreFactory;
use Guiziweb\SyliusAIPlatformBundle\Repository\VectorStoreConfigurationRepositoryInterface;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\AI\Store\StoreInterface;

/**
 * Provides vector store instances based on channel configuration.
 *
 * @author Camille Islasse
 */
final readonly class VectorStoreProvider
{
    public function __construct(
        private VectorStoreConfigurationRepositoryInterface $configurationRepository,
        private VectorStoreFactory $storeFactory,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Get the configured vector store for a channel.
     *
     * @throws \RuntimeException if no enabled configuration is found
     */
    public function getStoreForChannel(ChannelInterface $channel): StoreInterface
    {
        $configurations = $this->configurationRepository->findBy([
            'channel' => $channel,
            'enabled' => true,
        ]);

        if (empty($configurations)) {
            throw new \RuntimeException(sprintf(
                'No enabled vector store configuration found for channel "%s".',
                $channel->getCode()
            ));
        }

        // Use the first enabled configuration
        $configuration = $configurations[0];

        $this->logger->info('Creating vector store from configuration', [
            'channel_code' => $channel->getCode(),
            'config_code' => $configuration->getCode(),
        ]);

        return $this->storeFactory->createFromConfiguration($configuration);
    }
}
