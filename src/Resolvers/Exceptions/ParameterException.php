<?php
declare(strict_types=1);

namespace Container\Core\Resolvers\Exceptions;

use ReflectionParameter as Parameter;

class ParameterException extends DependencyResolverException {

    public function __construct(Parameter $parameter, string $message = '', int $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            $message = sprintf('Cannot resolve parameter "%s"', $this->getParameterName($parameter));
        }

        parent::__construct($message, $code, $previous);
    }

    protected function getParameterName(Parameter $parameter): string {
        $function_name = $parameter->getDeclaringFunction()->getName();
        $parameter_name = $parameter->getName();

        return sprintf('%s(%s)', $function_name, $parameter_name);
    }
}