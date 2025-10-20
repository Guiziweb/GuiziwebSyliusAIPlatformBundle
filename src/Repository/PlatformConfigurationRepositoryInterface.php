<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Repository;

use Guiziweb\SyliusAIPlatformBundle\Entity\PlatformConfiguration;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of PlatformConfiguration
 *
 * @extends RepositoryInterface<T>
 */
interface PlatformConfigurationRepositoryInterface extends RepositoryInterface
{
}