# DI Container PoC

Simple implementation of Dependency Injection container for learning purpose only. `Psr\Container` compatible.

## Dependency injection methods

### Class constructor injection

```php
class UserService 
{
    private UserRepository $repository;
    
    public function __construct(UserRepository $repository) 
    {
        $this->repository = $repository;
    }
}
```

### Class property injection

```php
use Container\Core\Attributes\Inject;

class UserService 
{
    #[Inject]
    private UserRepository $repository;
}
```

### Class method injection

```php
use Container\Core\Attributes\Inject;

class UserService 
{
    private UserRepository $repository;

    #[Inject]
    public function setRepository(UserRepository $repository): void 
    {
        $this->repository = $repository;
    }
}
```

## Container methods

### `getInstance(): Container`

Get instance of Container.

```php
use Container\Core\Container;

$container = Container::getInstance();
```

### `get(string $id): object`

Get instance of given abstract.

```php
use Container\Core\Container;

$instance = Container::getInstance()->get(UserService::class);
```

### `has(string $id): bool`

Check if it has dependency.

```php
use Container\Core\Container;

$has_instance = Container::getInstance()->has(UserService::class);
```

### `make(string $abstract): object`

Make instance of given abstract.

```php
use Container\Core\Container;

$instance = Container::getInstance()->make(UserService::class);
```

### `bind(string $abstract, string|\Closure $definition): void`

Bind abstract with definition to container.

```php
use Container\Core\Container;

// only abstract
Container::getInstance()->bind(UserRepository::class);

// abstract and definition
Container::getInstance()->bind(UserRepositoryInterface::class, UserRepository::class);

// abstract and definition (closure)
Container::getInstance()->bind(UserRepositoryInterface::class, function(InMemoryDatabase $database) {
    return new UserRepository($database);
});
```

### `bindShared(string $abstract, string|\Closure $definition = null): void`

Bind abstract with definition to container, as singleton.

```php
use Container\Core\Container;

// only abstract
Container::getInstance()->bindShared(UserRepository::class);

// abstract and definition
Container::getInstance()->bindShared(UserRepositoryInterface::class, UserRepository::class);

// abstract and definition (closure)
Container::getInstance()->bindShared(UserRepositoryInterface::class, function(InMemoryDatabase $database) {
    return new UserRepository($database);
});
```

### `unbind(string $abstract): void`

Unbind abstract from container.

```php
use Container\Core\Container;

Container::getInstance()->unbind(UserService::class);
```
