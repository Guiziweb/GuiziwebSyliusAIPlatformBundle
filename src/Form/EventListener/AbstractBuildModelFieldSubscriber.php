<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Form\EventListener;

use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Enum\AiProvider;
use Guiziweb\SyliusAIPlatformBundle\Registry\AiProviderRegistry;
use Guiziweb\SyliusAIPlatformBundle\Repository\PlatformConfigurationRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Abstract subscriber for building dynamic model choice fields based on platform configuration.
 *
 * @author Camille Islasse
 */
abstract readonly class AbstractBuildModelFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @param PlatformConfigurationRepositoryInterface<PlatformConfiguration> $platformConfigurationRepository
     */
    public function __construct(
        protected AiProviderRegistry $providerRegistry,
        protected PlatformConfigurationRepositoryInterface $platformConfigurationRepository,
        protected FormFactoryInterface $formFactory,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $data = $event->getData();
        if (null === $data) {
            return;
        }

        $platformConfig = $this->getPlatformConfiguration($data);
        if (!$platformConfig instanceof PlatformConfiguration) {
            return;
        }

        $form = $event->getForm();
        $form->add($this->createModelChoiceField($platformConfig, $this->getModelValue($data)));
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        if (!is_array($data) || !array_key_exists('platformConfiguration', $data)) {
            return;
        }

        if ('' === $data['platformConfiguration']) {
            return;
        }

        /** @var PlatformConfiguration|null $platformConfig */
        $platformConfig = $this->platformConfigurationRepository->find($data['platformConfiguration']);
        if (null === $platformConfig || null === $platformConfig->getProvider()) {
            return;
        }

        $form = $event->getForm();
        $form->add($this->createModelChoiceField($platformConfig));
    }

    protected function createModelChoiceField(PlatformConfiguration $platformConfig, ?string $model = null): FormInterface
    {
        $providerValue = $platformConfig->getProvider();
        if (null === $providerValue) {
            return $this->formFactory->createNamed('model', ChoiceType::class, $model, [
                'auto_initialize' => false,
                'label' => $this->getTranslationPrefix() . '.model',
                'choices' => [],
                'placeholder' => $this->getTranslationPrefix() . '.select_model',
                'required' => true,
            ]);
        }

        $provider = AiProvider::tryFrom($providerValue);
        if (null === $provider) {
            return $this->formFactory->createNamed('model', ChoiceType::class, $model, [
                'auto_initialize' => false,
                'label' => $this->getTranslationPrefix() . '.model',
                'choices' => [],
                'placeholder' => $this->getTranslationPrefix() . '.select_model',
                'required' => true,
            ]);
        }

        $choices = $this->getModelChoices($provider);

        return $this->formFactory->createNamed('model', ChoiceType::class, $model, [
            'auto_initialize' => false,
            'label' => $this->getTranslationPrefix() . '.model',
            'choices' => $choices,
            'placeholder' => $this->getTranslationPrefix() . '.select_model',
            'required' => true,
        ]);
    }

    /**
     * Get the platform configuration from the entity data.
     */
    abstract protected function getPlatformConfiguration(mixed $data): ?PlatformConfiguration;

    /**
     * Get the model value from the entity data.
     */
    abstract protected function getModelValue(mixed $data): ?string;

    /**
     * Get model choices for the provider (LLM or Embedding models).
     *
     * @return array<string, string>
     */
    abstract protected function getModelChoices(AiProvider $provider): array;

    /**
     * Get the translation prefix for labels (e.g., 'guiziweb_sylius_ai_platform.form.agent_configuration').
     */
    abstract protected function getTranslationPrefix(): string;
}
