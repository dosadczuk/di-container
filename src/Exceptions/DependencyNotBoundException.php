<?php
declare(strict_types=1);

namespace Container\Core\Exceptions;

/**
 * @internal
 */
class DependencyNotBoundException extends ContainerException
{
    public function __construct(public readonly string $dependency)
    {
        parent::__construct("'$dependency' is not bound.");
    }
}
