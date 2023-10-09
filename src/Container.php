<?php

declare(strict_types=1);

namespace Poppables;

use InvalidArgumentException;
use Pimple\Container as PimpleContainer;
use Pimple\Exception\ExpectedInvokableException;
use Pimple\Exception\FrozenServiceException;
use Pimple\Exception\InvalidServiceIdentifierException;
use Pimple\Exception\UnknownIdentifierException;
use Poppables\Exception\ExpectedInvokable;
use Poppables\Exception\FrozenService;
use Poppables\Exception\InvalidServiceIdentifier;
use Poppables\Exception\UnknownIdentifier;
use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface
{
    private PimpleContainer $pimple;

    public function __construct(array $values = [])
    {
        if (array_key_exists(ContainerInterface::class, $values)) {
            throw new InvalidArgumentException('Values array must not contain an entry for ' . ContainerInterface::class);
        }

        $this->pimple = new PimpleContainer($values);
        $this->pimple[ContainerInterface::class] = $this;
    }

    public function get(string $id)
    {
        try {
            return $this->pimple[$id];
        } catch (UnknownIdentifierException $e) {
            throw new UnknownIdentifier($e->getMessage(), 0, $e);
        }
    }

    public function has(string $id): bool
    {
        return isset($this->pimple[$id]);
    }

    public function keys(): array
    {
        return $this->pimple->keys();
    }

    public function raw(string $id)
    {
        try {
            $raw = $this->pimple->raw($id);
        } catch (UnknownIdentifierException $e) {
            throw new UnknownIdentifier($e->getMessage(), 0, $e);
        }

        if ($raw instanceof Wrapped) {
            return $raw->getInvokable();
        }

        return $raw;
    }

    public function register(ServiceProvider $provider): self
    {
        $provider->register($this);

        return $this;
    }

    public function set(string $id, $value)
    {
        if ($id === ContainerInterface::class) {
            throw new InvalidArgumentException('Cannot set value for reserved identifier ' . ContainerInterface::class);
        }

        if (! $value instanceof Poppable) {
            if (is_object($value) && method_exists($value, '__invoke')) {
                $value = new Definition($value);
            } else {
                $value = new Parameter($value);
            }
        }

        try {
            $value->pop($id, $this->pimple);
        } catch (ExpectedInvokableException $e) {
            throw new ExpectedInvokable($e->getMessage(), $e->getCode(), $e);
        } catch (FrozenServiceException $e) {
            throw new FrozenService($e->getMessage(), $e->getCode(), $e);
        } catch (InvalidServiceIdentifierException $e) {
            throw new InvalidServiceIdentifier($e->getMessage(), $e->getCode(), $e);
        } catch (UnknownIdentifierException $e) {
            throw new UnknownIdentifier($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function unset(string $id)
    {
        if ($id === ContainerInterface::class) {
            throw new InvalidArgumentException('Cannot unset value for reserved identifier ' . ContainerInterface::class);
        }

        unset($this->pimple[$id]);
    }
}
