<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Fixture\Factory;

use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlatformConfigurationExampleFactory implements ExampleFactoryInterface
{
    private OptionsResolver $optionsResolver;

    public function __construct()
    {
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): PlatformConfiguration
    {
        $options = $this->optionsResolver->resolve($options);

        $platformConfiguration = new PlatformConfiguration();
        $platformConfiguration->setCode($options['code']);
        $platformConfiguration->setName($options['name']);
        $platformConfiguration->setEnabled($options['enabled']);
        $platformConfiguration->setProvider($options['provider']);
        $platformConfiguration->setApiKey($options['api_key']);

        if (null !== $options['settings']) {
            $platformConfiguration->setSettings($options['settings']);
        }

        return $platformConfiguration;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('code')
            ->setAllowedTypes('code', 'string')

            ->setRequired('name')
            ->setAllowedTypes('name', 'string')

            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')

            ->setRequired('provider')
            ->setAllowedTypes('provider', 'string')

            ->setRequired('api_key')
            ->setAllowedTypes('api_key', 'string')

            ->setDefault('settings', null)
            ->setAllowedTypes('settings', ['null', 'array'])
        ;
    }
}