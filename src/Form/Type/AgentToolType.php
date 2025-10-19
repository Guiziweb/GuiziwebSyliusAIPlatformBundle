<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Form\Type;

use Guiziweb\SyliusAIPlatformBundle\Entity\AgentTool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AgentToolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('toolName', HiddenType::class)
            ->add('enabled', CheckboxType::class, [
                'label' => 'Enabled',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AgentTool::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'guiziweb_sylius_ai_platform_agent_tool';
    }
}
