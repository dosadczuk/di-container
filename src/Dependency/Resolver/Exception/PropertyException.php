<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Exception;

use ReflectionProperty as Property;

class PropertyException extends DependencyResolverException {

    public function __construct(Property $property, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = "Cannot resolve property '\${$property->getName()}'";
        }

        parent::__construct($message, $code, $previous);
    }
}