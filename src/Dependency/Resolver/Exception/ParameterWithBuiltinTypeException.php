<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Exception;

use ReflectionParameter as Parameter;

final class ParameterWithBuiltinTypeException extends ParameterException {

    public function __construct(Parameter $parameter, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = "Cannot resolve builtin typed parameter '\${$parameter->getName()}' without default value";
        }

        parent::__construct($parameter, $message, $code, $previous);
    }
}