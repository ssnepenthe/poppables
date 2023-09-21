<?php

declare(strict_types=1);

namespace Poppables;

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
use RuntimeException;

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
        try {
            return $this->pimple[$id];
        } catch (UnknownIdentifierException $e) {
            throw new UnknownIdentifier($e->getMessage());
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
            throw new UnknownIdentifier($e->getMessage());
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
            throw new ExpectedInvokable($e->getMessage());
        } catch (FrozenServiceException $e) {
            throw new FrozenService($e->getMessage());
        } catch (InvalidServiceIdentifierException $e) {
            throw new InvalidServiceIdentifier($e->getMessage());
        } catch (UnknownIdentifierException $e) {
            throw new UnknownIdentifier($e->getMessage());
        }

        // @todo return?
    }

    public function unset(string $id)
    {
        unset($this->pimple[$id]);
    }

    // @todo service locator and iterator implementations?
}
