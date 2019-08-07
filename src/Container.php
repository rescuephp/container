<?php

declare(strict_types=1);

namespace Rescue\Container;

use Psr\Container\ContainerExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Rescue\Container\Exception\ContainerNotFoundException;
use function count;
use function is_string;

class Container implements ContainerInterface
{
    /**
     * @var object[]
     */
    private $storage = [];

    /**
     * @inheritDoc
     */
    public function add(string $id, string $className = null, array $params = [])
    {
        return $this->storage[$id] = $this->instance($className ?? $id, $params);
    }

    /**
     * @inheritDoc
     */
    public function addInstance(string $id, $instance)
    {
        if (is_callable($instance)) {
            return $this->storage[$id] = $instance($this);
        }

        return $this->storage[$id] = $instance;
    }

    /**
     * @inheritDoc
     * @throws ContainerExceptionInterface
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new ContainerNotFoundException("Entry $id not found");
        }

        return $this->storage[$id];
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return isset($this->storage[$id]);
    }

    /**
     * @param ReflectionClass $reflect
     * @param array $params
     * @return array
     *
     * @throws ReflectionException
     */
    private function resolveDependencies(ReflectionClass $reflect, array $params = []): array
    {
        $constructor = $reflect->getConstructor();

        if ($constructor instanceof ReflectionMethod) {
            if (count($params) < $constructor->getNumberOfRequiredParameters()) {
                foreach ($constructor->getParameters() as $constructParam) {
                    if ($constructParam->allowsNull()
                        || $constructParam->getType() === null
                    ) {
                        continue;
                    }

                    $params[] = $constructParam->getType()->getName();
                }
            }

            foreach ($params as &$param) {
                if (!is_string($param)) {
                    continue;
                }

                if (isset($this->storage[$param])) {
                    $param = $this->storage[$param];

                    continue;
                }

                if (!class_exists($param)) {
                    continue;
                }

                $this->add($param);

                foreach ($this->storage as $object) {
                    if ($object instanceof $param) {
                        $param = $object;

                        continue;
                    }
                }
            }
        }

        return $params;
    }

    /**
     * @param string $className
     * @param array $params
     * @return object
     * @throws ReflectionException
     */
    private function instance(string $className, array $params = [])
    {
        $reflect = new ReflectionClass($className);

        $params = $this->resolveDependencies($reflect, $params);

        return $reflect->newInstanceArgs($params);
    }
}
