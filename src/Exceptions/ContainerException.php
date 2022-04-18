<?php
declare(strict_types=1);

namespace Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;

/**
 * @internal
 */
class ContainerException extends \RuntimeException implements ContainerExceptionInterface
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        if (!empty($message)) {
            $message = "[Container]: $message";
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Create {@see ContainerException} from another thrown exception.
     */
    public static function fromThrowable(\Throwable $e): static
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}
