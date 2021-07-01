<?php
declare(strict_types=1);

namespace Foundation\Container\Resolvers\Exceptions;

use ReflectionParameter as Parameter;

final class ParameterWithUnionTypeException extends ParameterException {

    public function __construct(Parameter $parameter, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = sprintf('Cannot resolve union typed parameter "%s"', $this->getParameterName($parameter));
        }

        parent::__construct($parameter, $message, $code, $previous);
    }
}