<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Guiziweb\SyliusAIPlatformBundle\Entity\VectorStoreConfiguration;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;

/**
 * @extends ServiceEntityRepository<VectorStoreConfiguration>
 * @implements VectorStoreConfigurationRepositoryInterface<VectorStoreConfiguration>
 */
final class VectorStoreConfigurationRepository extends ServiceEntityRepository implements VectorStoreConfigurationRepositoryInterface
{
    use ResourceRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VectorStoreConfiguration::class);
    }
}
