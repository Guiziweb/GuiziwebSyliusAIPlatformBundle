<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Twig\Component\VectorStoreConfiguration;

use Guiziweb\SyliusAIPlatformBundle\Entity\VectorStoreConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Form\Type\VectorStoreConfigurationType;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\PreReRender;

/**
 * @use ResourceFormComponentTrait<VectorStoreConfiguration>
 */
#[AsLiveComponent(name: 'guiziweb:vector_store_configuration:form', template: '@GuiziwebSyliusAIPlatform/components/VectorStoreConfiguration/Form.html.twig', route: 'sylius_admin_live_component')]
#[AutoconfigureTag('sylius.live_component.admin', ['key' => 'guiziweb:vector_store_configuration:form'])]
final class FormComponent
{
    /** @use ResourceFormComponentTrait<VectorStoreConfiguration> */
    use ResourceFormComponentTrait;

    use TemplatePropTrait;

    /**
     * @param RepositoryInterface<VectorStoreConfiguration> $vectorStoreConfigurationRepository
     */
    public function __construct(
        #[Autowire(service: 'guiziweb.repository.vector_store_configuration')]
        RepositoryInterface $vectorStoreConfigurationRepository,
        FormFactoryInterface $formFactory,
    ) {
        $this->initialize(
            $vectorStoreConfigurationRepository,
            $formFactory,
            VectorStoreConfiguration::class,
            VectorStoreConfigurationType::class,
        );
    }

    #[PreReRender(priority: -100)]
    public function updateForm(): void
    {
        $this->form = null;
    }
}