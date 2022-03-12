<?php
declare(strict_types=1);

namespace Container\Exceptions;

/**
 * @internal
 */
class DependencyNotExistsException extends ContainerException
{
    public function __construct(public readonly string $dependency)
    {
        parent::__construct("'$dependency' not exists.");
    }
}
