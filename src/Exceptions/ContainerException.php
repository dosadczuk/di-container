<?php
declare(strict_types=1);

namespace Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
class ContainerException extends \RuntimeException implements ContainerExceptionInterface
{
    /**
     * Create {@see ContainerException} from another thrown exception.
     */
    public static function fromThrowable(\Throwable $e): static
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}
