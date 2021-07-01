<?php
declare(strict_types=1);

namespace Foundation\Container\Resolvers\Exceptions;

use ReflectionProperty as Property;

class PropertyException extends DependencyResolverException {

    public function __construct(Property $property, $message = "", $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = sprintf('Cannot resolve property "%s"', $this->getPropertyName($property));
        }

        parent::__construct($message, $code, $previous);
    }

    protected function getPropertyName(Property $property): string {
        $class_name = $property->getDeclaringClass()->getShortName();
        $property_name = $property->getName();

        return sprintf('%s::%s', $class_name, $property_name);
    }
}