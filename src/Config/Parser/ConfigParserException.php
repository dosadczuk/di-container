<?php
declare(strict_types=1);

namespace Container\Core\Config\Parser;

final class ConfigParserException extends \RuntimeException {

    public static function fromException(\Exception $e): self {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}