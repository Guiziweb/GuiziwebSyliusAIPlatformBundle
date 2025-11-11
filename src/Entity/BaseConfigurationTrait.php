<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Entity;

use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * Common methods for AI-related configurations.
 *
 * @author Camille Islasse
 */
trait BaseConfigurationTrait
{
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
}