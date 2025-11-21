<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\DependencyInjection;

use Guiziweb\SyliusAIPlatformBundle\Tool\ToolInterface;
use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class GuiziwebSyliusAIPlatformBundleExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    /** @psalm-suppress UnusedVariable */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        $loader->load('services.yaml');

        // Auto-tag tools implementing ToolInterface
        $container->registerForAutoconfiguration(ToolInterface::class)
            ->addTag('guiziweb.ai_tool');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);
        $this->prependSyliusResource($container);
    }

    private function prependSyliusResource(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('sylius_resource', [
            'mapping' => [
                'paths' => [
                    dirname(__DIR__) . '/Entity',
                ],
            ],
        ]);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'Guiziweb\SyliusAIPlatformBundle\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@GuiziwebSyliusAIPlatformBundle/src/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return [
            'Sylius\Bundle\CoreBundle\Migrations',
        ];
    }
}
