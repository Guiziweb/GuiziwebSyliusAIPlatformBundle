<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Twig;

use Guiziweb\SyliusAIPlatformBundle\Service\ToolMetadataExtractor;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ToolNameExtension extends AbstractExtension
{
    public function __construct(
        private readonly ToolMetadataExtractor $toolMetadataExtractor,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('tool_short_name', [$this, 'getToolShortName']),
            new TwigFilter('tool_description', [$this, 'getToolDescription']),
        ];
    }

    public function getToolShortName(?string $toolClassName): string
    {
        if (null === $toolClassName) {
            return 'Unknown Tool';
        }

        $parts = explode('\\', $toolClassName);

        return end($parts);
    }

    public function getToolDescription(?string $toolClassName): ?string
    {
        if (null === $toolClassName || !class_exists($toolClassName)) {
            return null;
        }

        return $this->toolMetadataExtractor->getToolDescription($toolClassName);
    }
}
