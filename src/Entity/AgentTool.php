<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity]
#[ORM\Table(name: 'guiziweb_ai_agent_tool')]
class AgentTool implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: AgentConfiguration::class, inversedBy: 'agentTools')]
    #[ORM\JoinColumn(name: 'agent_configuration_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?AgentConfiguration $agentConfiguration = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $toolName = null;

    #[ORM\Column(type: 'boolean')]
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
