<?php
declare(strict_types=1);

namespace Foundation\Container\Resolvers\Exceptions;

use ReflectionProperty as Property;

final class PropertyNotTypedException extends PropertyException {

    public function __construct(Property $property, $message = "", $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = sprintf('Cannot resolve not typed property "%s"', $this->getPropertyName($property));
        }

        parent::__construct($message, $code, $previous);
    }
}