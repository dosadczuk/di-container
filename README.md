# DI Container PoC

Simple implementation of Dependency Injection container for learning purpose only.

## Dependency injection methods

<details>
<summary>Definition of used classes</summary>
<p>

```php
class UserRepository {
    public function getUsers(): array {
        return [ 'Bob', 'Alice' ];
    }
}
```

</p>
</details>

### Class constructor injection

```php
class UserService {

    private UserRepository $repository;
    
    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }
}
```

### Class property injection

```php
use Container\Core\Attribute\Inject;

class UserService {

    #[Inject]
    private UserRepository $repository;
}
```

### Class setter injection

```php
use Container\Core\Attribute\Inject;

class UserService {

    private UserRepository $repository;
 
    #[Inject]
    public function setRepository(UserRepository $repository): void {
        $this->repository = $repository; 
    }
}
```

## Class instantiation

### Using Container method

```php
use Container\Core\Container;

$user_service = Container::getInstance()->make(UserService::class);
```

### Using function

```php
use function Container\Core\make;

$user_service = make(UserService::class);
```

## Class registration

<details>
<summary>Definition of used classes</summary>
<p>

```php
interface UserRepositoryInterface {
    public function getUsers(): array;
}

class UserRepository implements UserRepositoryInterface {
    public function getUsers(): array {
        return [ 'Bob', 'Alice' ];
    }
}

class UserService {

    private UserRepositoryInterface $repository;
    
    public function __construct(UserRepositoryInterface $repository) {
        $this->repository = $repository;
    }
} 
```

</p>
</details>

### Transient dependency

```php
use Container\Core\Container;
use function Container\Core\register;

// using Container method
Container::getInstance()->register(
    UserRepositoryInterface::class, 
    UserRepository::class
);

// or using function
register(
    UserRepositoryInterface::class,
    UserRepository::class
);
```

### Shared dependency

```php
use Container\Core\Container;
use function Container\Core\register_shared;

// using Container method
Container::getInstance()->registerShared(
    UserRepositoryInterface::class, 
    UserRepository::class
);

// or using function
register_shared(
    UserRepositoryInterface::class,
    UserRepository::class
);
```

## Class registration (with closure)

<details>
<summary>Definition of used classes</summary>
<p>

```php
interface DatabaseInterface {
    public function query(string $query): array;
}

class InMemoryDatabase implements DatabaseInterface {
    public function query(string $query): array {
        // some db query logic
    }
}

class UserRepository {

    private DatabaseInterface $database;
    
    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }
    
    public function getUsers(): array {
        return $this->database->query('SELECT * FROM users');
    }  
}

class UserService {
    
    private UserRepository $repository;
    
    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }
    
    public function getUsers(): array {
        return $this->repository->getUsers();
    }
}
```

</p>
</details>

### Transient dependency

```php
use function Container\Core\register;

register(UserService::class, function(InMemoryDatabase $database) {
    $repository = new UserRepository($database);

    return new UserService($repository);
});
```

### Shared dependency

```php
use function Container\Core\register_shared;

register_shared(UserService::class, function(InMemoryDatabase $database) {
    $repository = new UserRepository($database);

    return new UserService($repository);
});
```
