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
        if (is_object($value) && method_exists($value, '__invoke')) {
            $value = new Definition($value);
        }

        if (! $value instanceof Poppable) {
            $value = new Parameter($value);
        }

        $value->pop($id, $this->pimple);

        // @todo return?
    }

    // @todo service locator and iterator implementations?
}
