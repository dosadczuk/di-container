<?php
declare(strict_types=1);

namespace Container\Exceptions;

/**
 * @internal
 */
class DependencyAlreadyAddedException extends ContainerException
{
    public function __construct(public readonly string $dependency)
    {
        parent::__construct("'$dependency' already added.");
    }
}
