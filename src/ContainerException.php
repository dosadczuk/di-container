<?php
declare(strict_types=1);

namespace Container\Core;

class ContainerException extends \RuntimeException {

    public static function fromThrowable(\Throwable $e): self {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}