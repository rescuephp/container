<?php

declare(strict_types=1);

namespace Rescue\Container;

use Psr\Container\ContainerInterface as PsrContainerInterfaceAlias;
use ReflectionException;

interface ContainerInterface extends PsrContainerInterfaceAlias
{
    /**
     * @param string $id
     * @param string|null $className
     * @param array $params
     * @return mixed
     *
     * @throws ReflectionException
     */
    public function add(string $id, string $className = null, array $params = []);

    /**
     * @param string $id
     * @param callable|object $instance
     * @return mixed
     */
    public function addInstance(string $id, $instance);
}
