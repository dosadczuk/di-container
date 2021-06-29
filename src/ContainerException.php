<?php
declare(strict_types=1);

namespace Foundation\Container;

class ContainerException extends \RuntimeException {

    public static function fromException(\Throwable $throwable): static {
        return new static(
            $throwable->getMessage(),
            $throwable->getCode(),
            $throwable
        );
    }
}