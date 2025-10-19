<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Form\Type;

use Guiziweb\SyliusAIPlatformBundle\Entity\AgentConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Form\EventListener\BuildAgentFormSubscriber;
use Guiziweb\SyliusAIPlatformBundle\Form\EventListener\PopulateAgentToolsSubscriber;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AgentConfigurationType extends AbstractType
{
    public function __construct(
        private readonly BuildAgentFormSubscriber $buildAgentFormSubscriber,
        private readonly PopulateAgentToolsSubscriber $populateAgentToolsSubscriber,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.channel',
                'required' => true,
            ])
            ->add('code', TextType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.code',
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.name',
                'required' => true,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.enabled',
                'required' => false,
            ])
            ->add('platformConfiguration', EntityType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.platform_configuration',
                'class' => PlatformConfiguration::class,
                'choice_label' => fn (PlatformConfiguration $config) => sprintf(
                    '%s%s',
                    $config->getProvider() ?? 'N/A',
                    $config->isEnabled() ? '' : ' (disabled)',
                ),
                'placeholder' => 'guiziweb_sylius_ai_platform.form.agent_configuration.select_platform',
                'required' => true,
            ])
            ->add('systemPrompt', TextareaType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.system_prompt',
                'required' => false,
                'attr' => [
                    'rows' => 10,
                ],
            ])
            ->add('agentTools', CollectionType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.agent_configuration.tools',
                'entry_type' => AgentToolType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
            ])
            ->addEventSubscriber($this->buildAgentFormSubscriber)
            ->addEventSubscriber($this->populateAgentToolsSubscriber)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AgentConfiguration::class,
        ]);
    }
}
