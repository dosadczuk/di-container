<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver;

final class DependencyResolverException extends \RuntimeException {

    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = 'Cannot resolve dependency';
        }

        parent::__construct($message, $code, $previous);
    }

    public static final function fromReflectionException(\ReflectionException $e): self {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}