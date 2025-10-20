<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Entity;

use Guiziweb\SyliusAIPlatformBundle\Form\Type\PlatformConfigurationType;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;
use Guiziweb\SyliusAIPlatformBundle\Validator\Constraints\Code;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['code'], message: 'guiziweb_sylius_ai_platform.platform_configuration.code.unique')]
#[AsResource(
    alias: 'guiziweb.platform_configuration',
    section: 'admin',
    formType: PlatformConfigurationType::class,
    templatesDir: '@SyliusAdmin/shared/crud',
    routePrefix: '/admin',
    operations: [
        new Index(grid: 'guiziweb_admin_platform_configuration'),
        new Create(),
        new Update(),
        new Show(),
        new Delete(),
    ],
)]
class PlatformConfiguration implements ResourceInterface
{
    private ?int $id = null;

    #[Code]
    private ?string $code = null;

    #[Assert\NotBlank(message: 'guiziweb_sylius_ai_platform.platform_configuration.name.not_blank')]
    private ?string $name = null;

    private bool $enabled = false;

    #[Assert\NotBlank(message: 'guiziweb_sylius_ai_platform.platform_configuration.provider.not_blank')]
    private ?string $provider = null;

    #[Assert\NotBlank(message: 'guiziweb_sylius_ai_platform.platform_configuration.api_key.not_blank')]
    private ?string $apiKey = null;

    private ?array $settings = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings(?array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }
}
