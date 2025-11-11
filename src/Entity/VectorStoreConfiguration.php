<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Entity;

use Guiziweb\SyliusAIPlatformBundle\Form\Type\VectorStoreConfigurationType;
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

#[UniqueEntity(fields: ['code'], message: 'guiziweb_sylius_ai_platform.vector_store_configuration.code.unique')]
#[AsResource(
    alias: 'guiziweb.vector_store_configuration',
    section: 'admin',
    formType: VectorStoreConfigurationType::class,
    templatesDir: '@SyliusAdmin/shared/crud',
    routePrefix: '/admin',
    operations: [
        new Index(grid: 'guiziweb_admin_vector_store_configuration'),
        new Create(),
        new Update(),
        new Show(),
        new Delete(),
    ],
)]
class VectorStoreConfiguration implements ResourceInterface
{
    use BaseConfigurationTrait;

    private ?int $id = null;

    #[Assert\NotNull(message: 'guiziweb_sylius_ai_platform.vector_store_configuration.channel.not_blank')]
    private ?ChannelInterface $channel = null;

    #[Code]
    private ?string $code = null;

    #[Assert\NotBlank(message: 'guiziweb_sylius_ai_platform.vector_store_configuration.name.not_blank')]
    private ?string $name = null;

    private bool $enabled = false;

    #[Assert\NotNull(message: 'guiziweb_sylius_ai_platform.vector_store_configuration.platform_configuration.not_null')]
    private ?PlatformConfiguration $platformConfiguration = null;

    #[Assert\NotBlank(message: 'guiziweb_sylius_ai_platform.vector_store_configuration.model.not_blank')]
    private ?string $model = null;

    private ?string $distanceMetric = 'cosine';

    public function getDistanceMetric(): ?string
    {
        return $this->distanceMetric;
    }

    public function setDistanceMetric(?string $distanceMetric): self
    {
        $this->distanceMetric = $distanceMetric;

        return $this;
    }
}