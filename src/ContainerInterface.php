<?php

namespace Rescue\Container;

use ReflectionException;
use Rescue\Container\Exception\ContainerExceptionInterface;
use Rescue\Container\Exception\NotFoundExceptionInterface;

interface ContainerInterface
{
    /**
     * @param string $id
     * @param string|null $className
     * @param array $params
     * @return object
     * @throws ReflectionException
     */
    public function append(string $id, string $className = null, array $params = []);

    /**
     * @param string $id
     * @param callable $callback
     * @return object
     */
    public function appendByCallback(string $id, callable $callback);

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     */
    public function get(string $id);

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool;
}
