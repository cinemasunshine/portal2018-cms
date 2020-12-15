<?php

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
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
