<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Provider;

use Guiziweb\SyliusAIPlatformBundle\Factory\PlatformFactory;
use Guiziweb\SyliusAIPlatformBundle\Repository\VectorStoreConfigurationRepositoryInterface;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\AI\Store\Document\Vectorizer;
use Symfony\AI\Store\Document\VectorizerInterface;

/**
 * Provides vectorizer instances based on channel configuration.
 *
 * @author Camille Islasse
 */
final readonly class VectorizerProvider
{
    public function __construct(
        private VectorStoreConfigurationRepositoryInterface $configurationRepository,
        private PlatformFactory $platformFactory,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Get the configured vectorizer for a channel.
     *
     * @throws \RuntimeException if no enabled configuration is found or model is not configured
     */
    public function getVectorizerForChannel(ChannelInterface $channel): VectorizerInterface
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

        $platformConfiguration = $configuration->getPlatformConfiguration();
        if (null === $platformConfiguration) {
            throw new \RuntimeException(sprintf(
                'No platform configuration found for vector store "%s".',
                $configuration->getCode()
            ));
        }

        $model = $configuration->getModel();
        if (null === $model || '' === trim($model)) {
            throw new \RuntimeException(sprintf(
                'No embedding model configured for vector store "%s".',
                $configuration->getCode()
            ));
        }

        $this->logger->info('Creating vectorizer from configuration', [
            'channel_code' => $channel->getCode(),
            'config_code' => $configuration->getCode(),
            'platform_provider' => $platformConfiguration->getProvider(),
            'model' => $model,
        ]);

        $platform = $this->platformFactory->createFromConfiguration($platformConfiguration);

        return new Vectorizer($platform, $model, $this->logger);
    }
}