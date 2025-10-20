<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class AgentTool implements ResourceInterface
{
    private ?int $id = null;

    private ?AgentConfiguration $agentConfiguration = null;

    private ?string $toolName = null;

    private bool $enabled = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgentConfiguration(): ?AgentConfiguration
    {
        return $this->agentConfiguration;
    }

    public function setAgentConfiguration(?AgentConfiguration $agentConfiguration): self
    {
        $this->agentConfiguration = $agentConfiguration;

        return $this;
    }

    public function getToolName(): ?string
    {
        return $this->toolName;
    }

    public function setToolName(?string $toolName): self
    {
        $this->toolName = $toolName;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
