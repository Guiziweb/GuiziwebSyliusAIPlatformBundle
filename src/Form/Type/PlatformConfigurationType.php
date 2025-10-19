<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Form\Type;

use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Registry\AiProviderRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlatformConfigurationType extends AbstractType
{
    public function __construct(
        private readonly AiProviderRegistry $providerRegistry,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.platform_configuration.code',
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.platform_configuration.name',
                'required' => true,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.platform_configuration.enabled',
                'required' => false,
            ])
            ->add('provider', ChoiceType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.platform_configuration.provider',
                'choices' => $this->providerRegistry->getAvailableProviders(),
                'placeholder' => 'guiziweb_sylius_ai_platform.form.platform_configuration.select_provider',
                'required' => true,
            ])
            ->add('apiKey', TextType::class, [
                'label' => 'guiziweb_sylius_ai_platform.form.platform_configuration.api_key',
                'required' => false,
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlatformConfiguration::class,
        ]);
    }
}
