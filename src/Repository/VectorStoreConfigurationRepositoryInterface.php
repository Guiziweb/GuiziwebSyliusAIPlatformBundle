<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Repository;

use Guiziweb\SyliusAIPlatformBundle\Entity\VectorStoreConfiguration;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of VectorStoreConfiguration
 *
 * @extends RepositoryInterface<T>
 */
interface VectorStoreConfigurationRepositoryInterface extends RepositoryInterface
{
}