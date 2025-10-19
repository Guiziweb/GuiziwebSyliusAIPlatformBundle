<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Enum;

use Symfony\AI\Platform\Bridge\AiMlApi\PlatformFactory as AiMlApiPlatformFactory;
use Symfony\AI\Platform\Bridge\Albert\PlatformFactory as AlbertPlatformFactory;
use Symfony\AI\Platform\Bridge\Anthropic\PlatformFactory as AnthropicPlatformFactory;
use Symfony\AI\Platform\Bridge\Azure\OpenAi\PlatformFactory as AzureOpenAiPlatformFactory;
use Symfony\AI\Platform\Bridge\Bedrock\PlatformFactory as BedrockPlatformFactory;
use Symfony\AI\Platform\Bridge\Cerebras\PlatformFactory as CerebrasPlatformFactory;
use Symfony\AI\Platform\Bridge\DeepSeek\PlatformFactory as DeepSeekPlatformFactory;
use Symfony\AI\Platform\Bridge\DockerModelRunner\PlatformFactory as DockerModelRunnerPlatformFactory;
use Symfony\AI\Platform\Bridge\ElevenLabs\PlatformFactory as ElevenLabsPlatformFactory;
use Symfony\AI\Platform\Bridge\Gemini\PlatformFactory as GeminiPlatformFactory;
use Symfony\AI\Platform\Bridge\HuggingFace\PlatformFactory as HuggingFacePlatformFactory;
use Symfony\AI\Platform\Bridge\LmStudio\PlatformFactory as LmStudioPlatformFactory;
use Symfony\AI\Platform\Bridge\Mistral\PlatformFactory as MistralPlatformFactory;
use Symfony\AI\Platform\Bridge\Ollama\PlatformFactory as OllamaPlatformFactory;
use Symfony\AI\Platform\Bridge\OpenAi\PlatformFactory as OpenAiPlatformFactory;
use Symfony\AI\Platform\Bridge\OpenRouter\PlatformFactory as OpenRouterPlatformFactory;
use Symfony\AI\Platform\Bridge\Perplexity\PlatformFactory as PerplexityPlatformFactory;
use Symfony\AI\Platform\Bridge\Replicate\PlatformFactory as ReplicatePlatformFactory;
use Symfony\AI\Platform\Bridge\Scaleway\PlatformFactory as ScalewayPlatformFactory;
use Symfony\AI\Platform\Bridge\TransformersPhp\PlatformFactory as TransformersPhpPlatformFactory;
use Symfony\AI\Platform\Bridge\VertexAi\PlatformFactory as VertexAiPlatformFactory;
use Symfony\AI\Platform\Bridge\Voyage\PlatformFactory as VoyagePlatformFactory;

enum AiProvider: string
{
    case OPENAI = 'openai';
    case ANTHROPIC = 'anthropic';
    case MISTRAL = 'mistral';
    case GEMINI = 'gemini';
    case OLLAMA = 'ollama';
    case AZURE_OPENAI = 'azure_openai';
    case CEREBRAS = 'cerebras';
    case DEEPSEEK = 'deepseek';
    case ELEVEN_LABS = 'eleven_labs';
    case LMSTUDIO = 'lmstudio';
    case OPENROUTER = 'openrouter';
    case PERPLEXITY = 'perplexity';
    case VERTEXAI = 'vertexai';
    case VOYAGE = 'voyage';
    case BEDROCK = 'bedrock';
    case REPLICATE = 'replicate';
    case SCALEWAY = 'scaleway';
    case HUGGINGFACE = 'huggingface';
    case TRANSFORMERS_PHP = 'transformers_php';
    case DOCKER_MODEL_RUNNER = 'docker_model_runner';
    case AIMLAPI = 'aimlapi';
    case ALBERT = 'albert';

    public function getLabel(): string
    {
        return match ($this) {
            self::OPENAI => 'OpenAI',
            self::ANTHROPIC => 'Anthropic (Claude)',
            self::MISTRAL => 'Mistral AI',
            self::GEMINI => 'Google Gemini',
            self::OLLAMA => 'Ollama',
            self::AZURE_OPENAI => 'Azure OpenAI',
            self::CEREBRAS => 'Cerebras',
            self::DEEPSEEK => 'DeepSeek',
            self::ELEVEN_LABS => 'ElevenLabs',
            self::LMSTUDIO => 'LM Studio',
            self::OPENROUTER => 'OpenRouter',
            self::PERPLEXITY => 'Perplexity',
            self::VERTEXAI => 'Google Vertex AI',
            self::VOYAGE => 'Voyage AI',
            self::BEDROCK => 'AWS Bedrock',
            self::REPLICATE => 'Replicate',
            self::SCALEWAY => 'Scaleway',
            self::HUGGINGFACE => 'HuggingFace',
            self::TRANSFORMERS_PHP => 'Transformers PHP',
            self::DOCKER_MODEL_RUNNER => 'Docker Model Runner',
            self::AIMLAPI => 'AIML API',
            self::ALBERT => 'Albert',
        };
    }

    public function getFactoryClass(): string
    {
        return match ($this) {
            self::OPENAI => OpenAiPlatformFactory::class,
            self::ANTHROPIC => AnthropicPlatformFactory::class,
            self::MISTRAL => MistralPlatformFactory::class,
            self::GEMINI => GeminiPlatformFactory::class,
            self::OLLAMA => OllamaPlatformFactory::class,
            self::AZURE_OPENAI => AzureOpenAiPlatformFactory::class,
            self::CEREBRAS => CerebrasPlatformFactory::class,
            self::DEEPSEEK => DeepSeekPlatformFactory::class,
            self::ELEVEN_LABS => ElevenLabsPlatformFactory::class,
            self::LMSTUDIO => LmStudioPlatformFactory::class,
            self::OPENROUTER => OpenRouterPlatformFactory::class,
            self::PERPLEXITY => PerplexityPlatformFactory::class,
            self::VERTEXAI => VertexAiPlatformFactory::class,
            self::VOYAGE => VoyagePlatformFactory::class,
            self::BEDROCK => BedrockPlatformFactory::class,
            self::REPLICATE => ReplicatePlatformFactory::class,
            self::SCALEWAY => ScalewayPlatformFactory::class,
            self::HUGGINGFACE => HuggingFacePlatformFactory::class,
            self::TRANSFORMERS_PHP => TransformersPhpPlatformFactory::class,
            self::DOCKER_MODEL_RUNNER => DockerModelRunnerPlatformFactory::class,
            self::AIMLAPI => AiMlApiPlatformFactory::class,
            self::ALBERT => AlbertPlatformFactory::class,
        };
    }

    public function getModelCatalogClass(): string
    {
        return match ($this) {
            self::OPENAI => \Symfony\AI\Platform\Bridge\OpenAi\ModelCatalog::class,
            self::ANTHROPIC => \Symfony\AI\Platform\Bridge\Anthropic\ModelCatalog::class,
            self::MISTRAL => \Symfony\AI\Platform\Bridge\Mistral\ModelCatalog::class,
            self::GEMINI => \Symfony\AI\Platform\Bridge\Gemini\ModelCatalog::class,
            self::OLLAMA => \Symfony\AI\Platform\Bridge\Ollama\ModelCatalog::class,
            self::AZURE_OPENAI => \Symfony\AI\Platform\Bridge\Azure\OpenAi\ModelCatalog::class,
            self::CEREBRAS => \Symfony\AI\Platform\Bridge\Cerebras\ModelCatalog::class,
            self::DEEPSEEK => \Symfony\AI\Platform\Bridge\DeepSeek\ModelCatalog::class,
            self::ELEVEN_LABS => \Symfony\AI\Platform\Bridge\ElevenLabs\ModelCatalog::class,
            self::LMSTUDIO => \Symfony\AI\Platform\Bridge\LmStudio\ModelCatalog::class,
            self::OPENROUTER => \Symfony\AI\Platform\Bridge\OpenRouter\ModelCatalog::class,
            self::PERPLEXITY => \Symfony\AI\Platform\Bridge\Perplexity\ModelCatalog::class,
            self::VERTEXAI => \Symfony\AI\Platform\Bridge\VertexAi\ModelCatalog::class,
            self::VOYAGE => \Symfony\AI\Platform\Bridge\Voyage\ModelCatalog::class,
            self::BEDROCK => \Symfony\AI\Platform\Bridge\Bedrock\ModelCatalog::class,
            self::REPLICATE => \Symfony\AI\Platform\Bridge\Replicate\ModelCatalog::class,
            self::SCALEWAY => \Symfony\AI\Platform\Bridge\Scaleway\ModelCatalog::class,
            self::HUGGINGFACE => \Symfony\AI\Platform\Bridge\HuggingFace\ModelCatalog::class,
            self::TRANSFORMERS_PHP => \Symfony\AI\Platform\Bridge\TransformersPhp\ModelCatalog::class,
            self::DOCKER_MODEL_RUNNER => \Symfony\AI\Platform\Bridge\DockerModelRunner\ModelCatalog::class,
            self::AIMLAPI => \Symfony\AI\Platform\Bridge\AiMlApi\ModelCatalog::class,
            self::ALBERT => \Symfony\AI\Platform\Bridge\Albert\ModelCatalog::class,
        };
    }

    public function isAvailable(): bool
    {
        return class_exists($this->getFactoryClass());
    }

    public function requiresApiKey(): bool
    {
        return match ($this) {
            self::OLLAMA, self::LMSTUDIO, self::DOCKER_MODEL_RUNNER => false,
            default => true,
        };
    }

    /**
     * @return array<string, string>
     */
    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $provider) {
            if ($provider->isAvailable()) {
                $choices[$provider->getLabel()] = $provider->value;
            }
        }

        return $choices;
    }
}
