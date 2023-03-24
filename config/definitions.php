<?php
/**
 * definitions of DI rules
 */

use Psr\Container\ContainerInterface;
use Tests\Sample\ClassA;
use Tests\Sample\SampleInterface;

return [
    SampleInterface::class => function(ContainerInterface $container) {
        return $container->get(ClassA::class);
    }
];