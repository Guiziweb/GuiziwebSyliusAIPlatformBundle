<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Guiziweb\SyliusAIPlatformBundle\Form\Type\AgentConfigurationType;
use Guiziweb\SyliusAIPlatformBundle\Validator\Constraints\Code;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['code'], message: 'guiziweb_sylius_ai_platform.agent_configuration.code.unique')]
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
    use BaseConfigurationTrait;

    private ?int $id = null;

    #[Assert\NotNull(message: 'guiziweb_sylius_ai_platform.agent_configuration.channel.not_blank')]
    private ?ChannelInterface $channel = null;

    #[Code]
    private ?string $code = null;

    #[Assert\NotBlank(message: 'guiziweb_sylius_ai_platform.agent_configuration.name.not_blank')]
    private ?string $name = null;

    private bool $enabled = false;

    #[Assert\NotNull(message: 'guiziweb_sylius_ai_platform.agent_configuration.platform_configuration.not_blank')]
    private ?PlatformConfiguration $platformConfiguration = null;

    #[Assert\NotBlank(message: 'guiziweb_sylius_ai_platform.agent_configuration.model.not_blank')]
    private ?string $model = null;

    private ?string $systemPrompt = null;

    /** @var Collection<int, AgentTool> */
    private Collection $agentTools;

    public function __construct()
    {
        $this->agentTools = new ArrayCollection();
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
