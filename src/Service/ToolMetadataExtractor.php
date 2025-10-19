<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Service;

use Symfony\AI\Agent\Toolbox\Attribute\AsTool;

final class ToolMetadataExtractor
{
    /**
     * @param class-string $toolClassName
     */
    public function getToolDescription(string $toolClassName): ?string
    {
        try {
            $reflection = new \ReflectionClass($toolClassName);
            $attributes = $reflection->getAttributes(AsTool::class);

            if (count($attributes) > 0) {
                /** @var AsTool $asTool */
                $asTool = $attributes[0]->newInstance();

                return $asTool->description;
            }
        } catch (\ReflectionException) {
            // Return null if class doesn't exist or has no attribute
        }

        return null;
    }
}
