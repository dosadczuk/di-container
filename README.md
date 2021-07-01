# DI Container PoC

Simple implementation of Dependency Injection container for learning purpose only.

## Dependency injection methods

### Constructor injection

```php
class UserRepository {
    public function getUsers(): array {
        return [ 'Bob', 'Alice' ];
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

### Class property injection

```php
use Foundation\Container\Attribute\Inject;

class UserRepository {
    public function getUsers(): array {
        return [ 'Bob', 'Alice' ];
    }
}

class UserService {

    #[Inject]
    private UserRepository $repository;
    
    public function getUsers(): array {
        return $this->repository->getUsers();
    }
}
```

### UserService instantiation

#### Using Container static method

```php
use Foundation\Container\Container;

$user_service = Container::get(UserService::class);
```

#### Using Container non-static method

```php
use Foundation\Container\Container;

$user_service = Container::getInstance()->make(UserService::class);
```

#### Using function

```php
use function Foundation\Container\{get,make};

$user_service = get(UserService::class);
// or
$user_service = make(UserService::class);
```

## Class registration

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

### Shared dependency

```php
use Foundation\Container\Container;
use function Foundation\Container\register_shared;

// using Container instance and non-static method
$container = Container::getInstance();
$container->registerShared(
    UserRepositoryInterface::class, 
    UserRepository::class
);

// or using function
register_shared(
    UserRepositoryInterface::class,
    UserRepository::class
);
```

### Transient dependency

```php
use Foundation\Container\Container;
use function Foundation\Container\register;

// using Container instance and non-static method
$container = Container::getInstance();
$container->register(
    UserRepositoryInterface::class, 
    UserRepository::class
);

// or using function
register(
    UserRepositoryInterface::class,
    UserRepository::class
);
```

```php
use function Foundation\Container\get;

$user_service = get(UserService::class);
```

## Class registration (with closure)

```php
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

```php
use function Foundation\Container\{register_shared};

register_shared(UserService::class, function() {
    $repository = new UserRepository(new InMemoryDatabase());

    return new UserService($repository);
});
```

### More complex example

```php
interface MailerInterface {
    public function send(string $recipient, string $title, string $message): int;
}

class InternalMailer implements MailerInterface {
    // ... some logic

    public function send(string $recipient, string $title, string $message): int {}
}

class ExternalMailer implements MailerInterface {
    // ... some logic

    public function send(string $recipient, string $title, string $message): int {}
}

class InternalUserService {

    private MailerInterface $mailer;
    
    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }
    
    public function sendMeetingReminder(): void {
        // some logic to send email with $this->mailer
    }
}

class ExternalUserService {

    private MailerInterface $mailer;
    
    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }
    
    public function sendNewsletter(): void {
        // some logic to sent email with $this->mailer
    }
}
```

```php
use function Foundation\Container\register_shared;

register_shared(InternalUserService::class, function(InternalMailer $mailer) {
    return new InternalUserService($mailer);
});

register_shared(ExternalUserService::class, function(ExternalMailer $mailer) {
    return new ExternalUserService($mailer);
});
```
