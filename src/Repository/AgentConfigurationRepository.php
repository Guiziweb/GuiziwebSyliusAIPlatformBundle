<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Guiziweb\SyliusAIPlatformBundle\Entity\AgentConfiguration;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;

/**
 * @extends ServiceEntityRepository<AgentConfiguration>
 * @implements AgentConfigurationRepositoryInterface<AgentConfiguration>
 */
final class AgentConfigurationRepository extends ServiceEntityRepository implements AgentConfigurationRepositoryInterface
{
    use ResourceRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgentConfiguration::class);
    }
}
