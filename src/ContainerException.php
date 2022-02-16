<?php
declare(strict_types=1);

namespace Container\Core;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @internal
 */
final class ContainerException extends \RuntimeException implements ContainerExceptionInterface
{
    /**
     * Create {@see ContainerException} from another thrown exception.
     */
    public static function fromThrowable(\Throwable $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }

    public static function notFound(string $dependency): self
    {
        $exception = new class ($dependency) extends \RuntimeException implements NotFoundExceptionInterface {
            public function __construct(string $dependency)
            {
                parent::__construct("Dependency '{$dependency}' not found.");
            }
        };

        return self::fromThrowable($exception);
    }
}
