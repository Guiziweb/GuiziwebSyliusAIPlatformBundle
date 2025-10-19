<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Guiziweb\SyliusAIPlatformBundle\Form\Type\AgentConfigurationType;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

#[ORM\Entity(repositoryClass: \Guiziweb\SyliusAIPlatformBundle\Repository\AgentConfigurationRepository::class)]
#[ORM\Table(name: 'guiziweb_ai_agent_configuration')]
#[AsResource(
    alias: 'guiziweb.agent_configuration',
    section: 'admin',
    formType: AgentConfigurationType::class,
    routePrefix: '/admin',
    templatesDir: '@SyliusAdmin/shared/crud',
    operations: [
        new Index(grid: 'guiziweb_admin_agent_configuration'),
        new Create(),
        new Update(),
        new Show(),
        new Delete(),
    ],
)]
class AgentConfiguration implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ChannelInterface::class)]
    #[ORM\JoinColumn(name: 'channel_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?ChannelInterface $channel = null;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private ?string $code = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    #[ORM\ManyToOne(targetEntity: PlatformConfiguration::class)]
    #[ORM\JoinColumn(name: 'platform_configuration_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?PlatformConfiguration $platformConfiguration = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $model = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $systemPrompt = null;

    /** @var Collection<int, AgentTool> */
    #[ORM\OneToMany(mappedBy: 'agentConfiguration', targetEntity: AgentTool::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $agentTools;

    public function __construct()
    {
        $this->agentTools = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getPlatformConfiguration(): ?PlatformConfiguration
    {
        return $this->platformConfiguration;
    }

    public function setPlatformConfiguration(?PlatformConfiguration $platformConfiguration): self
    {
        $this->platformConfiguration = $platformConfiguration;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getSystemPrompt(): ?string
    {
        return $this->systemPrompt;
    }

    public function setSystemPrompt(?string $systemPrompt): self
    {
        $this->systemPrompt = $systemPrompt;

        return $this;
    }

    /**
     * @return Collection<int, AgentTool>
     */
    public function getAgentTools(): Collection
    {
        return $this->agentTools;
    }

    public function addAgentTool(AgentTool $agentTool): self
    {
        if (!$this->agentTools->contains($agentTool)) {
            $this->agentTools->add($agentTool);
            $agentTool->setAgentConfiguration($this);
        }

        return $this;
    }

    public function removeAgentTool(AgentTool $agentTool): self
    {
        if ($this->agentTools->removeElement($agentTool)) {
            if ($agentTool->getAgentConfiguration() === $this) {
                $agentTool->setAgentConfiguration(null);
            }
        }

        return $this;
    }

    /**
     * Get only enabled tools.
     *
     * @return array<AgentTool>
     */
    public function getEnabledTools(): array
    {
        return $this->agentTools->filter(fn (AgentTool $tool) => $tool->isEnabled())->toArray();
    }
}
