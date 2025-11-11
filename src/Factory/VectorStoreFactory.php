<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Factory;

use Guiziweb\SyliusAIPlatformBundle\Entity\VectorStoreConfiguration;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\AI\Store\Bridge\Local\CacheStore;
use Symfony\AI\Store\Bridge\Local\DistanceCalculator;
use Symfony\AI\Store\Bridge\Local\DistanceStrategy;
use Symfony\AI\Store\StoreInterface;

/**
 * Factory for creating vector stores from configuration.
 *
 * @author Camille Islasse
 */
final readonly class VectorStoreFactory
{
    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {
    }

    public function createFromConfiguration(VectorStoreConfiguration $config): StoreInterface
    {
        if (!$config->isEnabled()) {
            throw new \RuntimeException('Cannot create store from disabled configuration.');
        }

        $distanceMetric = $config->getDistanceMetric()
            ? DistanceStrategy::from($config->getDistanceMetric())
            : DistanceStrategy::COSINE_DISTANCE;

        $calculator = new DistanceCalculator($distanceMetric);

        return new CacheStore($this->cache, $calculator, 'vectors_' . $config->getCode());
    }
}