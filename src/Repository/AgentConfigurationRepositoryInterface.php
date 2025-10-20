<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Repository;

use Guiziweb\SyliusAIPlatformBundle\Entity\AgentConfiguration;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of AgentConfiguration
 *
 * @extends RepositoryInterface<T>
 */
interface AgentConfigurationRepositoryInterface extends RepositoryInterface
{
}