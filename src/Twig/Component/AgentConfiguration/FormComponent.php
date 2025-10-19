<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Twig\Component\AgentConfiguration;

use Guiziweb\SyliusAIPlatformBundle\Entity\AgentConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Form\Type\AgentConfigurationType;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\PreReRender;

/**
 * @use ResourceFormComponentTrait<AgentConfiguration>
 */
#[AsLiveComponent(name: 'guiziweb:agent_configuration:form', template: '@GuiziwebSyliusAIPlatform/components/AgentConfiguration/Form.html.twig')]
#[AutoconfigureTag('sylius.live_component.admin', ['key' => 'guiziweb:agent_configuration:form'])]
final class FormComponent
{
    /** @use ResourceFormComponentTrait<AgentConfiguration> */
    use ResourceFormComponentTrait;

    use TemplatePropTrait;

    /**
     * @param RepositoryInterface<AgentConfiguration> $agentConfigurationRepository
     */
    public function __construct(
        #[Autowire(service: 'guiziweb.repository.agent_configuration')]
        RepositoryInterface $agentConfigurationRepository,
        FormFactoryInterface $formFactory,
    ) {
        $this->initialize(
            $agentConfigurationRepository,
            $formFactory,
            AgentConfiguration::class,
            AgentConfigurationType::class,
        );
    }

    #[PreReRender(priority: -100)]
    public function updateForm(): void
    {
        $this->form = null;
    }
}
