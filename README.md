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
use Container\Attributes\Inject;

class UserService 
{
    #[Inject]
    private UserRepository $repository;
}
```

### Class method injection

```php
use Container\Attributes\Inject;

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

### Get instance of Container

```php
use Container\Container;
use function Container\container;

$container = Container::getInstance();
// or
$container = container();
```

### Get instance of given abstract

```php
use Container\Container;
use function Container\get;

$instance = Container::getInstance()->get(UserService::class);
// or
$instance = get(UserService::class);
```

### Check if it has dependency

```php
use Container\Container;
use function \Container\has;

$has_instance = Container::getInstance()->has(UserService::class);
// or
$has_instance = has(UserService::class);
```

### Make instance of given abstract

```php
use Container\Container;
use function Container\make;

$instance = Container::getInstance()->make(UserService::class);
// or
$instance = make(UserService::class);
```

### Bind abstract with definition to container

```php
use Container\Container;
use function Container\bind;

// only abstract
Container::getInstance()->bind(UserRepository::class);
// or
bind(UserRepository::class);

// abstract and definition
Container::getInstance()->bind(UserRepositoryInterface::class, UserRepository::class);
// or
bind(UserRepositoryInterface::class, UserRepository::class);

// abstract and definition (closure)
Container::getInstance()->bind(UserRepositoryInterface::class, function(InMemoryDatabase $database) {
    return new UserRepository($database);
});
// or
bind(UserRepositoryInterface::class, function(InMemoryDatabase $database) {
    return new UserRepository($database);
});
```

### Bind abstract with definition to container, as singleton

```php
use Container\Container;
use function Container\bind_shared;

// only abstract
Container::getInstance()->bindShared(UserRepository::class);
// or
bind_shared(UserRepository::class);

// abstract and definition
Container::getInstance()->bindShared(UserRepositoryInterface::class, UserRepository::class);
// or
bind_shared(UserRepositoryInterface::class, UserRepository::class);

// abstract and definition (closure)
Container::getInstance()->bindShared(UserRepositoryInterface::class, function(InMemoryDatabase $database) {
    return new UserRepository($database);
});
// or
bind_shared(UserRepositoryInterface::class, function(InMemoryDatabase $database) {
    return new UserRepository($database);
});
```

### Unbind abstract from container

```php
use Container\Container;
use function Container\unbind;

Container::getInstance()->unbind(UserService::class);
// or
unbind(UserService::class);
```
