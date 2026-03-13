<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as FrameworkTestCase;
use ReflectionObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class TestCase extends FrameworkTestCase
{
    /** Obtém o valor de uma propriedade, mesmo que ela seja restrita */
    public function getPropertyValue(object $instance, string $name): mixed
    {
        $reflection = new ReflectionObject($instance);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($instance);
    }
}
