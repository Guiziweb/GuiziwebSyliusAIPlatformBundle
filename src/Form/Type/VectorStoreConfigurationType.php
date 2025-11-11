<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Form\Type;

use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Entity\VectorStoreConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Form\EventListener\BuildVectorStoreFormSubscriber;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Symfony\AI\Store\Bridge\Local\DistanceStrategy;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VectorStoreConfigurationType extends AbstractType
{
    public function __construct(
        private readonly BuildVectorStoreFormSubscriber $buildVectorStoreFormSubscriber,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $distanceMetrics = [];
        foreach (DistanceStrategy::cases() as $case) {
            $distanceMetrics[ucfirst($case->value)] = $case->value;
        }

        $builder
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.vector_store_configuration.channel',
                'required' => true,
            ])
            ->add('code', TextType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.vector_store_configuration.code',
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.vector_store_configuration.name',
                'required' => true,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.vector_store_configuration.enabled',
                'required' => false,
            ])
            ->add('platformConfiguration', EntityType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.vector_store_configuration.platform_configuration',
                'class' => PlatformConfiguration::class,
                'choice_label' => fn (PlatformConfiguration $config) => sprintf(
                    '%s%s',
                    $config->getProvider() ?? 'N/A',
                    $config->isEnabled() ? '' : ' (disabled)',
                ),
                'placeholder' => 'guiziweb_sylius_ai_platform.form.vector_store_configuration.select_platform',
                'required' => true,
            ])
            ->add('distanceMetric', ChoiceType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.vector_store_configuration.distance_metric',
                'choices' => $distanceMetrics,
                'placeholder' => 'guiziweb_sylius_ai_platform.form.vector_store_configuration.select_distance_metric',
                'required' => false,
            ])
            ->addEventSubscriber($this->buildVectorStoreFormSubscriber)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VectorStoreConfiguration::class,
        ]);
    }
}