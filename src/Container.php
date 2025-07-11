<?php

declare(strict_types=1);

namespace RobertWesner\DependencyInjection;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;
use RobertWesner\DependencyInjection\Attributes\AutowireInterface;
use RobertWesner\DependencyInjection\Attributes\BufferFile;
use RobertWesner\DependencyInjection\Attributes\FileBasedAutowireInterface;
use RobertWesner\DependencyInjection\Exception\AutowireException;
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
     * @throws AutowireException
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
     * @throws AutowireException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function resolveClass(string $name)
    {
        if (!$this->exists($name)) {
            return null;
        }

        $class = new ReflectionClass($name);
        $constructor = $class->getConstructor();

        $parameters = [];
        foreach ($constructor?->getParameters() ?? [] as $parameter) {
            if ($parameter?->getType()?->getName() !== null && $this->exists($parameter->getType()->getName())) {
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
                $parameters[] = $this->resolveAutowireAttribute($parameter);
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
            if (!($result === null && $parameter->getType()->allowsNull())) {
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
