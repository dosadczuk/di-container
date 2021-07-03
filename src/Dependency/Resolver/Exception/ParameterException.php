<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Exception;

use ReflectionParameter as Parameter;

class ParameterException extends DependencyResolverException {

    public function __construct(Parameter $parameter, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = "Cannot resolve parameter '\${$parameter->getName()}'";
        }

        parent::__construct($message, $code, $previous);
    }
}