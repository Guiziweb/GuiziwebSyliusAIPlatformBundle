<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Form\EventListener;

use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Entity\VectorStoreConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Enum\AiProvider;

final readonly class BuildVectorStoreFormSubscriber extends AbstractBuildModelFieldSubscriber
{
    protected function getPlatformConfiguration(mixed $data): ?PlatformConfiguration
    {
        if (!$data instanceof VectorStoreConfiguration) {
            return null;
        }

        return $data->getPlatformConfiguration();
    }

    protected function getModelValue(mixed $data): ?string
    {
        if (!$data instanceof VectorStoreConfiguration) {
            return null;
        }

        return $data->getModel();
    }

    protected function getModelChoices(AiProvider $provider): array
    {
        return $this->providerRegistry->getEmbeddingModelChoicesForProvider($provider);
    }

    protected function getTranslationPrefix(): string
    {
        return 'guiziweb_sylius_ai_platform.form.vector_store_configuration';
    }
}