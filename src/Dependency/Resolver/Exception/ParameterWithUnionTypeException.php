<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Exception;

use ReflectionParameter as Parameter;

final class ParameterWithUnionTypeException extends ParameterException {

    public function __construct(Parameter $parameter, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = "Cannot resolve union typed parameter '\${$parameter->getName()}'";
        }

        parent::__construct($parameter, $message, $code, $previous);
    }
}