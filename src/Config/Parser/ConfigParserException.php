<?php
declare(strict_types=1);

namespace Container\Core\Config\Parser;

use Container\Core\ContainerException;

final class ConfigParserException extends ContainerException {

    public static function fromException(\Exception $e): self {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}