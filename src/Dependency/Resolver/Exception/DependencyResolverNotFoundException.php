<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Exception;

final class DependencyResolverNotFoundException extends DependencyResolverException {

    public function __construct(mixed $definition, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = "Cannot create dependency resolver for '$definition'";
        }

        parent::__construct($message, $code, $previous);
    }
}