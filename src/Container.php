<?php

declare(strict_types=1);

namespace Poppables;

use Pimple\Container as PimpleContainer;
use Psr\Container\ContainerInterface;
use RuntimeException;

// @todo implement arrayaccess?
final class Container implements ContainerInterface
{
    private PimpleContainer $pimple;

    public function __construct(array $values = [], ?PimpleContainer $pimple = null)
    {
        // @todo Should we really allow user to provide a container?
        $this->pimple = $pimple ?: new PimpleContainer();

        if (isset($this->pimple[ContainerInterface::class])) {
            throw new RuntimeException('@todo');
        }

        $this->pimple[ContainerInterface::class] = $this;

        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function get(string $id)
    {
        // @todo catch and rethrow exceptions?
        return $this->pimple[$id];
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
        $raw = $this->pimple->raw($id);

        if ($raw instanceof Invokable) {
            return $raw->getCallable();
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
        // @todo
        // To really match pimple this should probably be a test for invokable objects...
        // pimple doesn't allow all callables - only closures and invokables
        if (is_callable($value)) {
            $value = new Definition($value);
        }

        // @todo
        // if (! $value instanceof Poppable) {
        //     $value = new Raw($value);
        // }

        // $value->pop($id, $this->pimple);

        if ($value instanceof Poppable) {
            $value->pop($id, $this->pimple);
        } else {
            // @todo implement as "raw" poppable or something similar?
            $this->pimple[$id] = $value;
        }

        // @todo return?
    }

    // @todo service locator and iterator implementations?
}