<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'sylius.menu.admin.main', method: 'addAdminMenuItems')]
final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $aiSection = $menu
            ->addChild('ai')
            ->setLabel('sylius.menu.admin.main.configuration.ai')
            ->setLabelAttribute('icon', 'tabler:robot');

        $aiSection
            ->addChild('platform_configurations', [
                'route' => 'guiziweb_admin_platform_configuration_index',
            ])
            ->setLabel('guiziweb_sylius_ai_platform.menu.admin.platform_configurations')
            ->setLabelAttribute('icon', 'tabler:robot');

        $aiSection
            ->addChild('agents', [
                'route' => 'guiziweb_admin_agent_configuration_index',
            ])
            ->setLabel('guiziweb_sylius_ai_platform.menu.admin.agent_configurations')
            ->setLabelAttribute('icon', 'tabler:brain');
    }
}
