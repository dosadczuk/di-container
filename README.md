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
use Container\Core\Attribute\Inject;

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

### Class setter injection

```php
use Container\Core\Attribute\Inject;

class UserRepository {
    public function getUsers(): array {
        return [ 'Bob', 'Alice' ];
    }
}

class UserService {

    private UserRepository $repository;
 
    #[Inject]
    public function setRepository(UserRepository $repository): void {
        $this->repository = $repository; 
    }
    
    public function getUsers(): array {
        return $this->repository->getUsers();
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
use function Container\Core\{make};

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
use Container\Core\Container;
use function Container\Core\register_shared;

// using Container instance and non-static method
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

### Transient dependency

```php
use Container\Core\Container;
use function Container\Core\register;

// using Container instance and non-static method
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

```php
use function Container\Core\make;

$user_service = make(UserService::class);
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
use function Container\Core\{register_shared};

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
use function Container\Core\register_shared;

register_shared(InternalUserService::class, function(InternalMailer $mailer) {
    return new InternalUserService($mailer);
});

register_shared(ExternalUserService::class, function(ExternalMailer $mailer) {
    return new ExternalUserService($mailer);
});
```
