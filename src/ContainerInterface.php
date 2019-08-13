<?php

declare(strict_types=1);

namespace Rescue\Container;

use Psr\Container\ContainerInterface as PsrContainerInterfaceAlias;
use ReflectionException;

interface ContainerInterface extends PsrContainerInterfaceAlias
{
    /**
     * @param string $id
     * @param string|callable|object|null $class
     * @param array $params
     * @return mixed
     *
     * @throws ReflectionException
     */
    public function add(string $id, $class = null, array $params = []);
}
