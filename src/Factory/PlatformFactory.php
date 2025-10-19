<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Factory;

use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Enum\AiProvider;
use Symfony\AI\Platform\Bridge\Anthropic\PlatformFactory as AnthropicPlatformFactory;
use Symfony\AI\Platform\Bridge\Cerebras\PlatformFactory as CerebrasPlatformFactory;
use Symfony\AI\Platform\Bridge\DeepSeek\PlatformFactory as DeepSeekPlatformFactory;
use Symfony\AI\Platform\Bridge\ElevenLabs\PlatformFactory as ElevenLabsPlatformFactory;
use Symfony\AI\Platform\Bridge\Gemini\PlatformFactory as GeminiPlatformFactory;
use Symfony\AI\Platform\Bridge\LmStudio\PlatformFactory as LmStudioPlatformFactory;
use Symfony\AI\Platform\Bridge\Mistral\PlatformFactory as MistralPlatformFactory;
use Symfony\AI\Platform\Bridge\Ollama\PlatformFactory as OllamaPlatformFactory;
use Symfony\AI\Platform\Bridge\OpenAi\PlatformFactory as OpenAiPlatformFactory;
use Symfony\AI\Platform\Bridge\OpenRouter\PlatformFactory as OpenRouterPlatformFactory;
use Symfony\AI\Platform\Bridge\Perplexity\PlatformFactory as PerplexityPlatformFactory;
use Symfony\AI\Platform\Bridge\Voyage\PlatformFactory as VoyagePlatformFactory;
use Symfony\AI\Platform\PlatformInterface;
use Symfony\Component\HttpClient\EventSourceHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PlatformFactory
{
    public function __construct(
        private ?HttpClientInterface $httpClient = null,
    ) {
        if (null === $this->httpClient) {
            $this->httpClient = new EventSourceHttpClient();
        }
    }

    public function createFromConfiguration(PlatformConfiguration $configuration): PlatformInterface
    {
        if (!$configuration->isEnabled()) {
            throw new \RuntimeException('Cannot create platform from disabled configuration.');
        }

        $provider = AiProvider::from($configuration->getProvider());

        if (!$provider->isAvailable()) {
            throw new \RuntimeException(sprintf('Provider "%s" is not available.', $provider->value));
        }

        return match ($provider) {
            AiProvider::OPENAI => $this->createOpenAiPlatform($configuration),
            AiProvider::ANTHROPIC => $this->createAnthropicPlatform($configuration),
            AiProvider::MISTRAL => $this->createMistralPlatform($configuration),
            AiProvider::GEMINI => $this->createGeminiPlatform($configuration),
            AiProvider::OLLAMA => $this->createOllamaPlatform($configuration),
            AiProvider::CEREBRAS => $this->createCerebrasPlatform($configuration),
            AiProvider::DEEPSEEK => $this->createDeepSeekPlatform($configuration),
            AiProvider::ELEVEN_LABS => $this->createElevenLabsPlatform($configuration),
            AiProvider::LMSTUDIO => $this->createLmStudioPlatform($configuration),
            AiProvider::OPENROUTER => $this->createOpenRouterPlatform($configuration),
            AiProvider::PERPLEXITY => $this->createPerplexityPlatform($configuration),
            AiProvider::VOYAGE => $this->createVoyagePlatform($configuration),
            default => throw new \RuntimeException(sprintf('Provider "%s" is not yet supported.', $provider->value)),
        };
    }

    private function createOpenAiPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return OpenAiPlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function createAnthropicPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return AnthropicPlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function createMistralPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return MistralPlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function createGeminiPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return GeminiPlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function createOllamaPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $hostUrl = $configuration->getSettings()['host_url'] ?? 'http://localhost:11434';

        return OllamaPlatformFactory::create(
            hostUrl: $hostUrl,
            httpClient: $this->httpClient,
        );
    }

    private function createCerebrasPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return CerebrasPlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function createDeepSeekPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return DeepSeekPlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function createElevenLabsPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return ElevenLabsPlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function createLmStudioPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $hostUrl = $configuration->getSettings()['host_url'] ?? 'http://localhost:1234';

        return LmStudioPlatformFactory::create(
            hostUrl: $hostUrl,
            httpClient: $this->httpClient,
        );
    }

    private function createOpenRouterPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return OpenRouterPlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function createPerplexityPlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return PerplexityPlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function createVoyagePlatform(PlatformConfiguration $configuration): PlatformInterface
    {
        $this->validateApiKey($configuration);

        return VoyagePlatformFactory::create(
            apiKey: $configuration->getApiKey(),
            httpClient: $this->httpClient,
        );
    }

    private function validateApiKey(PlatformConfiguration $configuration): void
    {
        if (null === $configuration->getApiKey() || '' === trim($configuration->getApiKey())) {
            throw new \InvalidArgumentException('API key is required for this provider.');
        }
    }
}