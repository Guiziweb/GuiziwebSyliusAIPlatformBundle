<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Form\EventListener;

use Guiziweb\SyliusAIPlatformBundle\Entity\AgentConfiguration;
use Guiziweb\SyliusAIPlatformBundle\Entity\AgentTool;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PopulateAgentToolsSubscriber implements EventSubscriberInterface
{
    /**
     * @param iterable<object> $availableTools All tools tagged with guiziweb.ai_tool
     */
    public function __construct(
        private readonly iterable $availableTools,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        /** @var AgentConfiguration|null $agent */
        $agent = $event->getData();

        if (null === $agent) {
            $agent = new AgentConfiguration();
            $event->setData($agent);
        }

        // Get existing tool names
        $existingToolNames = [];
        foreach ($agent->getAgentTools() as $agentTool) {
            $existingToolNames[] = $agentTool->getToolName();
        }

        // Add missing tools to the agent (disabled by default)
        foreach ($this->availableTools as $tool) {
            $toolClassName = $tool::class;

            // Skip if already configured
            if (in_array($toolClassName, $existingToolNames, true)) {
                continue;
            }

            // Create a new AgentTool entry (disabled by default)
            $agentTool = new AgentTool();
            $agentTool->setToolName($toolClassName);
            $agentTool->setEnabled(false);

            $agent->addAgentTool($agentTool);
        }
    }
}
