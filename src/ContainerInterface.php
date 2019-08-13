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
     * @param callable $callback
     * @return mixed
     */
    public function addByCallback(string $id, callable $callback);

    /**
     * @param string $id
     * @param $instance
     * @return mixed
     */
    public function addByInstance(string $id, $instance);
}
