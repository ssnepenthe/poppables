# poppables

Unnecessary abstraction around the pimple di container in an attempt to normalize it to the psr container interface.

This has been written for fun/out of curiosity and probably shouldn't actually be used for anything...

## Installation

...

## Usage

It is probably best to start by familiarizing yourself with the [pimple documentation](https://github.com/silexphp/Pimple#readme).

First we create a container instance:

```php
use Poppable\Container;

$container = new Container();
```

### Defining Services

```php
use Psr\Container\ContainerInterface;

$container->set(
    'session_storage',
    fn (ContainerInterface $c) => new SessionStorage('SESSION_ID')
);
$container->set(
    'session',
    fn (ContainerInterface $c) => new Session($c->get('session_storage'))
);
```

### Defining Factory Services

If you want a new instance to be created every time you get a service from the container you should wrap your definition in a call to the `factory()` function.

```php
use function Poppables\factory;

$container->set(
    'session',
    factory(fn (ContainerInterface $c) => new Session($c->get('session_storage')))
);
```

### Defining Parameters

```php
$container->set('cookie_name', 'SESSION_ID');
$container->set('session_storage_class', 'SessionStorage');
```

Now the session storage service can be redefined:

```php
$container->set(
    'session_storage',
    fn (ContainerInterface $c) => new $c->get('session_storage_class')($c->get('cookie_name'))
);
```

### Protecting Parameters

If you want to store an anonymous function as a parameter it needs to be wrapped in a call to the `protect()` function.

```php
use function Poppables\protect;

$container->set(
    'random_func',
    protect(fn () => rand())
);
```


### Modifying Services after Definition

You can use the `extend()` function to modify a service definition after it has been defined.

```php
use function Poppables\extend;

$container->set(
    'session_storage',
    fn (ContainerInterface $c) => new $c->get('session_storage_class')($c->get('cookie_name'))
);

$container->set('session_storage', extend(function ($storage, ContainerInterface $c) {
    $storage->...();

    return $storage;
}));
```

### Fetching the Service Creation Function

```php
$container->set(
    'session',
    factory(fn (ContainerInterface $c) => new Session($c->get('session_storage')))
);

$container->raw('session');
```
