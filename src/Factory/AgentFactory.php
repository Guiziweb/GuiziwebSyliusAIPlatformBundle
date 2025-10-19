<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Factory;

use Guiziweb\SyliusAIPlatformBundle\Entity\AgentConfiguration;
use Symfony\AI\Agent\Agent;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Agent\InputProcessor\SystemPromptInputProcessor;
use Symfony\AI\Agent\Toolbox\AgentProcessor;
use Symfony\AI\Agent\Toolbox\Toolbox;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class AgentFactory
{
    /**
     * @param iterable<object> $tools
     */
    public function __construct(
        private readonly PlatformFactory $platformFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly iterable $tools = [],
    ) {
    }

    public function createFromEntity(AgentConfiguration $agentConfiguration): AgentInterface
    {
        $platformConfiguration = $agentConfiguration->getPlatformConfiguration();

        if (null === $platformConfiguration) {
            throw new \RuntimeException(sprintf(
                'No platform configuration found for agent "%s".',
                $agentConfiguration->getCode()
            ));
        }

        $platform = $this->platformFactory->createFromConfiguration($platformConfiguration);
        $model = $agentConfiguration->getModel();

        if (null === $model) {
            throw new \RuntimeException(sprintf(
                'No model configured for agent "%s".',
                $agentConfiguration->getCode()
            ));
        }

        // Filter tools based on agent configuration
        $enabledToolNames = array_map(
            fn ($agentTool) => $agentTool->getToolName(),
            $agentConfiguration->getEnabledTools()
        );

        $filteredTools = [];
        foreach ($this->tools as $tool) {
            $toolName = $tool::class;
            if (empty($enabledToolNames) || in_array($toolName, $enabledToolNames, true)) {
                $filteredTools[] = $tool;
            }
        }

        $toolbox = new Toolbox(
            $filteredTools,
            eventDispatcher: $this->eventDispatcher,
        );
        $inputProcessors = [];
        $outputProcessors = [];

        // Add system prompt processor if configured
        $systemPrompt = $agentConfiguration->getSystemPrompt();
        if (null !== $systemPrompt && '' !== trim($systemPrompt)) {
            $inputProcessors[] = new SystemPromptInputProcessor($systemPrompt, $toolbox);
        }

        // Add agent processor for tool execution (both input and output)
        $agentProcessor = new AgentProcessor($toolbox);
        $inputProcessors[] = $agentProcessor;
        $outputProcessors[] = $agentProcessor;

        return new Agent(
            platform: $platform,
            model: $model,
            inputProcessors: $inputProcessors,
            outputProcessors: $outputProcessors,
            name: $agentConfiguration->getCode(),
        );
    }
}
