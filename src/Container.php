<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use RobertWesner\DependencyInjection\Attributes\AutowireInterface;
use RobertWesner\DependencyInjection\Attributes\BufferFile;
use RobertWesner\DependencyInjection\Attributes\FileBasedAutowireInterface;
use RobertWesner\DependencyInjection\Exception\AutowireException;
use RobertWesner\DependencyInjection\Exception\CircularDependencyException;
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
     * @template T of mixed
     *
     * @param class-string<T> $id
     * @return T
     *
     * @throws AutowireException
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function get(string $id): mixed
    {
        return $this->getWithTrace($id, 0, []);
    }

    public function has(string $id): bool
    {
        return isset($this->registry[$id]);
    }

    /**
     * @throws AutowireException
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    private function getWithTrace(string $id, int $depth, array $trace): mixed
    {
        $instance = $this->registry[$id] ?? $this->resolveClass($id, $depth, $trace);
        if ($instance === null) {
            throw new NotFoundException(sprintf(
                'Could not find "%s" in container.',
                $id,
            ));
        }

        return $instance;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $name
     * @return T|null
     *
     * @throws AutowireException
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    private function resolveClass(string $name, int $depth, array $trace): ?object
    {
        if (!$this->exists($name)) {
            return null;
        }

        if (isset($trace[$name])) {
            throw new CircularDependencyException(
                sprintf(
                    'Detected circular dependency: %s',
                    implode(
                        ' > ',
                        array_map(
                            fn (string $c) => mb_substr(mb_strrchr($c, '\\'), 1),
                            [...array_slice(array_flip($trace), $trace[$name]), $name]
                        )
                    )
                ),
            );
        }

        $trace[$name] = $depth;

        try {
            $class = new ReflectionClass($name);
        } catch (ReflectionException $exception) {
            throw new ContainerException(
                sprintf('Could not reflect on class "%s".', $name),
                previous: $exception,
            );
        }
        $constructor = $class->getConstructor();

        $parameters = [];
        foreach ($constructor?->getParameters() ?? [] as $parameter) {
            $attributes = $parameter?->getAttributes() ?? [];
            if (
                count($attributes) > 0
                && array_any(
                    $attributes,
                    fn(ReflectionAttribute $attribute) => is_a(
                        $attribute->getName(),
                        AutowireInterface::class,
                        true,
                    ),
                )
            ) {
                $parameters[] = $this->resolveAutowireAttribute($parameter);
            } elseif ($parameter?->getType()?->getName() !== null && $this->exists($parameter->getType()->getName())) {
                $parameters[] = $this->getWithTrace($parameter->getType()->getName(), $depth + 1, $trace);
            } elseif ($parameter->isDefaultValueAvailable()) {
                $parameters[] = $parameter->getDefaultValue();
            } elseif ($parameter->getType() === null) {
                throw new ContainerException(sprintf(
                    'Could not autowire parameter "%s" without known type or Annotation in class "%s".',
                    $parameter->getName(),
                    $name,
                ));
            } else {
                throw new ContainerException(sprintf(
                    'Could not autowire parameter "%s" of type "%s" in class "%s".',
                    $parameter->getName(),
                    $parameter->getType()->getName(),
                    $name,
                ));
            }
        }

        $instance = new $name(...$parameters);
        $this->set($name, $instance);

        return $instance;
    }

    /**
     * @throws AutowireException
     * @throws ContainerException
     */
    private function resolveAutowireAttribute(ReflectionParameter $parameter): mixed
    {
        $bufferIfFileBased = array_any(
            $parameter->getAttributes(),
            fn (ReflectionAttribute $attribute) => $attribute->getName() === BufferFile::class,
        );

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

            $buffer = $bufferIfFileBased && is_a(
                $attribute->getName(),
                FileBasedAutowireInterface::class,
                true,
            );

            /** @var AutowireInterface $instance */
            $instance = $attribute->newInstance();
            $result = $instance->resolve($buffer);
            if (!($result === null && $parameter->getType()->allowsNull()) && $parameter->getType()->isBuiltin()) {
                settype($result, $parameter->getType()->getName());
            }

            return $result;
        }

        //@codeCoverageIgnoreStart
        // I don't think this happen due to the pre-check for existing Attributes before calling
        throw new ContainerException('This should not have happened. Something is very wrong.');
        //@codeCoverageIgnoreEnd
    }

    private function exists(string $class): bool
    {
        return class_exists($class) || interface_exists($class) || enum_exists($class);
    }
}
