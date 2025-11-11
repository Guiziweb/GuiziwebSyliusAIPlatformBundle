<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Registry;

use Guiziweb\SyliusAIPlatformBundle\Enum\AiProvider;
use Symfony\AI\Platform\Capability;
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
     * Get LLM model choices for a specific provider (for agent configuration).
     * LLM = models that can generate text and call tools.
     *
     * @return array<string, string> Array of model name => model name
     */
    public function getLLMModelChoicesForProvider(AiProvider $provider): array
    {
        $models = $this->getModelsForProvider($provider);

        $choices = [];
        foreach ($models as $modelName => $modelConfig) {
            if ($this->isLLMModel($modelConfig)) {
                $choices[$modelName] = $modelName;
            }
        }

        return $choices;
    }

    /**
     * Get embedding model choices for a specific provider (for vector store configuration).
     *
     * @return array<string, string> Array of model name => model name
     */
    public function getEmbeddingModelChoicesForProvider(AiProvider $provider): array
    {
        $models = $this->getModelsForProvider($provider);

        $choices = [];
        foreach ($models as $modelName => $modelConfig) {
            if ($this->isEmbeddingModel($modelConfig)) {
                $choices[$modelName] = $modelName;
            }
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

    /**
     * Check if a model is an LLM (Language Model for text generation and tool calling).
     * LLM = has OUTPUT_TEXT/OUTPUT_STREAMING + TOOL_CALLING.
     *
     * @param array{class: string, capabilities: list<Capability>} $modelConfig
     */
    private function isLLMModel(array $modelConfig): bool
    {
        $capabilities = $modelConfig['capabilities'] ?? [];

        $hasTextOutput = $this->hasAnyCapability($capabilities, [
            Capability::OUTPUT_TEXT,
            Capability::OUTPUT_STREAMING,
        ]);

        $hasToolCalling = $this->hasCapability($capabilities, Capability::TOOL_CALLING);

        // LLM must have text output AND tool calling
        // This excludes Whisper (has OUTPUT_TEXT but no TOOL_CALLING)
        return $hasTextOutput && $hasToolCalling;
    }

    /**
     * Check if a model is an embedding model (for vectorization).
     * Embedding models have only INPUT capabilities, no OUTPUT.
     *
     * @param array{class: string, capabilities: list<Capability>} $modelConfig
     */
    private function isEmbeddingModel(array $modelConfig): bool
    {
        $capabilities = $modelConfig['capabilities'] ?? [];

        // Embedding models have NO output capabilities
        $outputCapabilities = [
            Capability::OUTPUT_TEXT,
            Capability::OUTPUT_STREAMING,
            Capability::OUTPUT_IMAGE,
            Capability::OUTPUT_AUDIO,
            Capability::OUTPUT_STRUCTURED,
        ];

        foreach ($capabilities as $capability) {
            if (in_array($capability, $outputCapabilities, true)) {
                return false;
            }
        }

        // Must have at least one INPUT capability
        return !empty($capabilities);
    }

    /**
     * Check if model has a specific capability.
     *
     * @param list<Capability> $capabilities
     */
    private function hasCapability(array $capabilities, Capability $capability): bool
    {
        return in_array($capability, $capabilities, true);
    }

    /**
     * Check if model has any of the specified capabilities.
     *
     * @param list<Capability>  $capabilities
     * @param list<Capability> $searchFor
     */
    private function hasAnyCapability(array $capabilities, array $searchFor): bool
    {
        foreach ($searchFor as $capability) {
            if ($this->hasCapability($capabilities, $capability)) {
                return true;
            }
        }

        return false;
    }
}
