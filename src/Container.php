<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionAttribute;
use ReflectionClass;
use RobertWesner\DependencyInjection\Attributes\AutowireInterface;
use RobertWesner\DependencyInjection\Exception\ContainerException;
use RobertWesner\DependencyInjection\Exception\NotFoundException;

class Container implements ContainerInterface
{
    private array $registry = [];

    public function set(string $id, mixed $value): void
    {
        $this->registry[$id] = $value;
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $id)
    {
        $instance = $this->registry[$id] ?? $this->resolveClass($id);
        if ($instance === null) {
            throw new NotFoundException(sprintf(
                'Could not find "%s" in container.',
                $id,
            ));
        }

        return $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->registry[$id]);
    }

    /**
     * @template T
     * @param class-string<T> $name
     * @return T|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function resolveClass(string $name)
    {
        if (!class_exists($name)) {
            return null;
        }

        $class = new ReflectionClass($name);
        $constructor = $class->getConstructor();

        $parameters = [];
        foreach ($constructor?->getParameters() ?? [] as $parameter) {
            if (class_exists($parameter->getType()->getName())) {
                $parameters[] = $this->get($parameter->getType()->getName());
            } elseif (
                $parameter->getAttributes()
                && array_any(
                    $parameter->getAttributes(),
                    fn(ReflectionAttribute $attribute) => is_a(
                        $attribute->getName(),
                        AutowireInterface::class,
                        true,
                    ),
                )
            ) {
                $result = null;
                foreach ($parameter->getAttributes() as $attribute) {
                    if (
                        !is_a(
                            $attribute->getName(),
                            AutowireInterface::class,
                            true,
                        )
                    ) {
                        continue;
                    }

                    /** @var AutowireInterface $instance */
                    $instance = $attribute->newInstance();
                    $result = $instance->resolve();
                    settype($result, $parameter->getType()->getName());

                    break;
                }

                $parameters[] = $result;
            } elseif ($parameter->isDefaultValueAvailable()) {
                $parameters[] = $parameter->getDefaultValue();
            } else {
                throw new ContainerException(sprintf(
                    'Could not autowire parameter "%s" of type "%s" in class "%s".',
                    $parameter->getName(),
                    $parameter->getType()->getName(),
                    $name(),
                ));
            }
        }

        $instance = new $name(...$parameters);
        $this->set($name, $instance);

        return $instance;
    }
}
