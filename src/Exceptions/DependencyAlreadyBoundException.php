<?php
declare(strict_types=1);

namespace Container\Core\Exceptions;

/**
 * @internal
 */
class DependencyAlreadyBoundException extends ContainerException
{
    public function __construct(public readonly string $dependency)
    {
        parent::__construct("'$dependency' is already bound.");
    }
}
