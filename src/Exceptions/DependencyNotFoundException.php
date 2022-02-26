<?php
declare(strict_types=1);

namespace Container\Core\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

/**
 * @internal
 */
class DependencyNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    public function __construct(public readonly string $dependency)
    {
        parent::__construct("Dependency '{$dependency}' not found.");
    }
}
