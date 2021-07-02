<?php
declare(strict_types=1);

namespace Container\Core\Resolvers\Exceptions;

final class DependencyResolverNotFoundException extends DependencyResolverException {

    public function __construct(mixed $definition, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = sprintf('Cannot create dependency resolver for "%s"', $definition);
        }

        parent::__construct($message, $code, $previous);
    }
}