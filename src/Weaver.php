<?php

namespace Dispify\Weaver;

use Dispify\Weaver\Exception\AutowiringFailedException;
use Dispify\Weaver\Exception\ClassMismatchException;
use Dispify\Weaver\Exception\ServiceNotFoundException;
use Dispify\Weaver\Exception\UndefinedClassException;
use Psr\Container\ContainerInterface;

class Weaver implements ContainerInterface
{
    private $parameters = [];
    private $services = [];

    /**
     * @param string $className
     * @param array $args [optional] constructor's arguments
     * @return $this
     * @throws ServiceNotFoundException
     * @throws \Throwable
     */
    public function weave(string $className, array $args = [])
    {
        if (!\class_exists($className)) {
            throw new UndefinedClassException('Specified class does not exist: "' . $className . '"');
        }

        $this->services[\strtolower($className)] = $args;

        return $this;
    }

    private function instanciate(string $className, array $args = [])
    {
        $arguments = [];
        foreach (self::guessConstructorArgs($className) as $param) {
            if ($param->isVariadic()) {
                continue;
            }

            if (\array_key_exists($param->getName(), $args)) {
                $arguments[] = $args[$param->getName()];
                continue;
            }

            if (\array_key_exists($param->getPosition(), $args)) {
                $arguments[] = $args[$param->getPosition()];
                continue;
            }

            if ($param->getClass() !== null) {
                if (!$this->has($param->getClass()->getName())) {
                    $this->weave($param->getClass()->getName());
                }

                $arguments[] = $this->get($param->getClass()->getName());
                continue;
            }

            if (\array_key_exists($param->getName(), $this->parameters)) {
                $arguments[] = $this->parameters[$param->getName()];
                continue;
            }

            if ($param->isOptional()) {
                $arguments[] = $param->getDefaultValue();
                continue;
            }

            throw new AutowiringFailedException('Can not resolve argument $' . $param->getName() . ' of ' . $className);
        }

        return new $className(...$arguments);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     * @throws \Exception
     */
    public function set(string $key, $value)
    {
        if (!\is_object($value)) {
            $this->parameters[$key] = $value;
            return $this;
        }

        if (!$value instanceof $key) {
            throw new ClassMismatchException($key, $value);
        }
        $aliases = \array_merge(
            \class_parents($value),
            \class_implements($value),
            \function_exists('\Dispify\class_aliases') ? \Dispify\class_aliases($value) : [],
            [\get_class($value), $key]
        );

        foreach ($aliases as $alias) {
            $this->services[\strtolower($alias)] = $value;
        }

        return $this;
    }

    /**
     * @param string $className
     * @return \ReflectionParameter[]
     */
    private static function guessConstructorArgs(string $className): array
    {
        $refl = (new \ReflectionClass($className))->getConstructor();
        return $refl ? $refl->getParameters() : [];
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new ServiceNotFoundException('Service is not declared: "' . $id . '"');
        }
        $id = \strtolower($id);

        if (!\is_object($this->services[$id])) {
            $this->set($id, $this->instanciate($id, $this->services[$id]));
        }

        return $this->services[$id];
    }

    public function has($id)
    {
        return isset($this->services[\strtolower($id)]);
    }
}
