<?php
declare(strict_types=1);

namespace Container\Core\Config;

class ConfigParserException extends \RuntimeException {

    public static function fromException(\Exception $e): static {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}