<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Code extends Compound
{
    /**
     * @param array<mixed> $options
     */
    protected function getConstraints(array $options): array
    {
        return [
            new NotBlank(message: 'sylius.code.not_blank'),
            new Type('string'),
            new Regex(['pattern' => '/^[\w-]*$/'], message: 'sylius.code.invalid'),
        ];
    }
}
