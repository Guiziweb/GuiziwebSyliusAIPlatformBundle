<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Registry;

use Guiziweb\SyliusAIPlatformBundle\Enum\AiProvider;
use Symfony\AI\Platform\ModelCatalog\ModelCatalogInterface;

final readonly class AiProviderRegistry
{
    /**
     * Get all available providers.
     *
     * @return array<string, string> Array of provider value => label
     */
    public function getAvailableProviders(): array
    {
        return AiProvider::getChoices();
    }

    /**
     * Get available models for a specific provider.
     *
     * @return array<string, array{class: string, capabilities: list}>
     */
    public function getModelsForProvider(AiProvider $provider): array
    {
        if (!$provider->isAvailable()) {
            return [];
        }

        $catalogClass = $provider->getModelCatalogClass();

        if (!class_exists($catalogClass)) {
            return [];
        }

        /** @var ModelCatalogInterface $catalog */
        $catalog = new $catalogClass();

        return $catalog->getModels();
    }

    /**
     * Get model choices for a specific provider (for form select).
     *
     * @return array<string, string> Array of model name => model name
     */
    public function getModelChoicesForProvider(AiProvider $provider): array
    {
        $models = $this->getModelsForProvider($provider);

        $choices = [];
        foreach (array_keys($models) as $modelName) {
            $choices[$modelName] = $modelName;
        }

        return $choices;
    }

    /**
     * Get model choices grouped by provider.
     *
     * @return array<string, array<string, string>>
     */
    public function getModelChoicesGroupedByProvider(): array
    {
        $choices = [];

        foreach (AiProvider::cases() as $provider) {
            if (!$provider->isAvailable()) {
                continue;
            }

            $providerModels = $this->getModelChoicesForProvider($provider);
            if (!empty($providerModels)) {
                $choices[$provider->getLabel()] = $providerModels;
            }
        }

        return $choices;
    }

    /**
     * Check if a provider requires an API key.
     */
    public function providerRequiresApiKey(AiProvider $provider): bool
    {
        return $provider->requiresApiKey();
    }
}
