<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Exception;

use ReflectionProperty as Property;

final class PropertyNotTypedException extends PropertyException {

    public function __construct(Property $property, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = "Cannot resolve not typed property '\${$property->getName()}'";
        }

        parent::__construct($property, $message, $code, $previous);
    }
}