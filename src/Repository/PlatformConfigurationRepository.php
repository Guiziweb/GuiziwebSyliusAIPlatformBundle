<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;

/**
 * @extends ServiceEntityRepository<PlatformConfiguration>
 * @implements PlatformConfigurationRepositoryInterface<PlatformConfiguration>
 */
final class PlatformConfigurationRepository extends ServiceEntityRepository implements PlatformConfigurationRepositoryInterface
{
    use ResourceRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlatformConfiguration::class);
    }
}
