<?php
declare(strict_types=1);

namespace Container\Core\Dependency\Resolver\Exception;

use ReflectionParameter as Parameter;

final class ParameterWithBuiltinTypeException extends ParameterException {

    public function __construct(Parameter $parameter, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = sprintf('Cannot resolve builtin typed parameter "%s" without default value', $this->getParameterName($parameter));
        }

        parent::__construct($parameter, $message, $code, $previous);
    }
}