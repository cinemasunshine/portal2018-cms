<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;

/**
 * Abstract middleware class
 */
abstract class AbstractMiddleware
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * construct
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
