<?php
declare(strict_types=1);

namespace Container\Exceptions;

/**
 * @internal
 */
class DependencyCycleException extends ContainerException
{
    public function __construct(public readonly string $dependency)
    {
        parent::__construct("'$dependency' contains cyclic dependencies.");
    }
}
