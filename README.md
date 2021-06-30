# DI Container PoC

Simple implementation of Dependency Injection container for learning purpose only.

## Dependency injection methods

### Constructor injection

Injection via constructor is probably the most common one. Not only guarantees flexibility and testability, but also is the most natural from OOP
standpoint. Let's see example below:

```php
// Class used as dependency
class UserRepository {
    public function getUsers(): array {
        return [ 'Bob', 'Alice' ];
    }
}

// Class to instantiate with dependency as constructor parameter
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

UserRepository is required dependency. UserService needs repository to fetch users.

### Class property injection

Class with dependencies injected explicitly to its properties requires usage of Container. There is no other way to simply instantiate it with all
required dependencies. Of course Reflection API is the solution, but it's all what Container does. The only thing required is to indicate which
property (static type required) must be injected. You can do this using **#[Injected]** attribute. Let's see example below to understand it more:

```php
// Class used as dependency
use Foundation\Container\Attribute\Injected;

class UserRepository {
    public function getUsers(): array {
        return [ 'Bob', 'Alice' ];
    }
}

// Class to instantiate with dependency as class property
class UserService {

    #[Injected]
    private UserRepository $repository;
    
    public function getUsers(): array {
        return $this->repository->getUsers();
    }
}
```

UserRepository is required dependency. UserService needs repository to fetch users.

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

But what if You want to create instance with already instantiated dependencies? Just put associated array as methods second parameter. Key must be
exactly the same name as constructor parameter or class property.

```php
use Foundation\Container\Container;

$user_service = Container::get(UserService::class, ['repository' => new UserRepository()]);
```

## Class registration

What about interfaces and implementations. Of course there is a way to instantiate dependency typed with interface, but in that case registration is
required. Let's illustrate it with simple example shown below:

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

**_UserService_** requires **_UserRepositoryInterface_**, but interface cannot be instantiated, only implementation can. Implementation is defined,
and it's called **_UserRepository_**. Now Container has to know how to instantiate interface.

### Shared dependency

Shared dependency means - use the same instance every time. It's also called Singleton. In that case You're not only saying "register implementation
for interface", but also "I want to get the same **_UserRepository_** instance every time I instantiate **_UserRepositoryInterface_**".

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

Transient dependency means - use new every time. In that case You're registering implementation for interface and saying "I want to get new
**_UserRepository_** instance every time I instantiate **_UserRepositoryInterface_**".

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

And now **_UserService_** is instantiated with **_UserRepository_**:

```php
use function Foundation\Container\get;

$user_service = get(UserService::class);
```

## Class registration (with closure)

There is a way to tell Container not only how to instantiate interface, but also how to instantiate class. You can do this using anonymous function.
Let's see example below:

```php
// Class used as dependency
class UserRepository {

    private DatabaseInterface $database;
    
    public function __construct(DatabaseInterface $database) {
        $this->database = $database;
    }
    
    public function getUsers(): array {
        return $this->database->query('SELECT * FROM users');
    }  
}

// Class to instantiate with dependency
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

Register definition of **_UserService_**:

```php
use function Foundation\Container\register;

register(UserService::class, function() {
    $repository = new UserRepository(new InMemoryDatabase());

    return new UserService($repository);
});
```

So as You can see in the example above, You can define how to instantiate **_UserService_**. From now onwards, every time instance of
**_UserService_** is created - **_UserRepository_** with **_InMemoryDatabase_** is used and dependency.

And the same way to register shared dependency:

```php
use function Foundation\Container\register_shared;

register_shared(UserService::class, function() {
    $repository = new UserRepository(new InMemoryDatabase());

    return new UserService($repository);
});
```

### More complex example

Let's say You need to send email using two types of mailers. For external users - external provider, for internal users - Your own mailer. From
architecture perspective mailer is mailer, so You want to make services clean and use interface instead of implementation. Maybe one day You will need
to swap internal mailer with something else. Let's try to create sample solution:

```php
interface MailerInterface {
    public function send(string $recipient, string $title, string $message): int;
}

class InternalMailer implements MailerInterface {
    public function send(string $recipient, string $title, string $message) {
        // send email with internal mailer
    }
}

class ExternalMailer implements MailerInterface {
    public function send(string $recipient, string $title, string $message) {
        // send email with external mailer
    }
}

class InternalUserService {

    private MailerInterface $mailer;
    
    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }
    
    public function sendMeetingReminder(): void {
        // some login to send email with $this->mailer
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
use function Foundation\Container\register;

// register InternalUserService to be created with InternalMailer
register(InternalUserService::class, function(InternalMailer $mailer) {
    return new InternalUserService($mailer);
});

// register ExternalUserService to be created with ExternalMailer
register(ExternalUserService::class, function(ExternalMailer $mailer) {
    return new ExternalUserService($mailer);
});
```

As You can see, we've used respectively **_InternalMailer_** and **_ExternalMailer_** as closure parameters. Container is smart enough to inject them,
but the most important, solution is clean and easy to maintain. Let's say today is the day, and You need to exchange **_ExternalMailer_** with
**_UnicornCompanyMailer_**:

```php
// before
register(ExternalUserService::class, function(ExternalMailer $mailer) {
    return new ExternalUserService($mailer);
});

// after
register(ExternalUserService::class, function(UnicornCompanyMailer $mailer) {
    return new ExternalUserService($mailer);
});
```