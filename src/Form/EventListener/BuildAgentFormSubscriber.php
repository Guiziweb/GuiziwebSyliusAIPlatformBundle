<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Form\EventListener;

use Doctrine\Persistence\ObjectRepository;
use Guiziweb\SyliusAIPlatformBundle\Entity\AgentConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Enum\AiProvider;
use Guiziweb\SyliusAIPlatformBundle\Registry\AiProviderRegistry;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final readonly class BuildAgentFormSubscriber implements EventSubscriberInterface
{
    /**
     * @param ObjectRepository<PlatformConfiguration> $platformConfigurationRepository
     */
    public function __construct(
        private AiProviderRegistry $providerRegistry,
        #[Target('platform configuration repository')]
        private ObjectRepository $platformConfigurationRepository,
        private FormFactoryInterface $formFactory,
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
        /** @var AgentConfiguration|null $agent */
        $agent = $event->getData();
        if (null === $agent) {
            return;
        }

        $platformConfig = $agent->getPlatformConfiguration();
        if (null === $platformConfig) {
            return;
        }

        $form = $event->getForm();
        $form->add($this->createModelChoiceField($platformConfig, $agent->getModel()));
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

    private function createModelChoiceField(PlatformConfiguration $platformConfig, ?string $model = null): FormInterface
    {
        $providerValue = $platformConfig->getProvider();
        if (null === $providerValue) {
            return $this->formFactory->createNamed('model', ChoiceType::class, $model, [
                'auto_initialize' => false,
                'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.model',
                'choices' => [],
                'placeholder' => 'guiziweb_sylius_ai_platform.form.agent_configuration.select_model',
                'required' => true,
            ]);
        }

        $provider = AiProvider::tryFrom($providerValue);
        if (null === $provider) {
            return $this->formFactory->createNamed('model', ChoiceType::class, $model, [
                'auto_initialize' => false,
                'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.model',
                'choices' => [],
                'placeholder' => 'guiziweb_sylius_ai_platform.form.agent_configuration.select_model',
                'required' => true,
            ]);
        }

        $choices = $this->providerRegistry->getModelChoicesForProvider($provider);

        return $this->formFactory->createNamed('model', ChoiceType::class, $model, [
            'auto_initialize' => false,
            'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.model',
            'choices' => $choices,
            'placeholder' => 'guiziweb_sylius_ai_platform.form.agent_configuration.select_model',
            'required' => true,
        ]);
    }
}
