<?php
declare(strict_types=1);

namespace Container\Core\Config;

use Container\Core\ContainerException;

final class ConfigCreator {

    public static function createFromFileName(string $file_name, ?ConfigType $type = null): Config {
        if (($type ??= ConfigType::fromFileName($file_name)) === null) {
            throw new ContainerException("Cannot determine config type for file '$file_name'");
        }

        return $type->getParser($file_name)->parse();
    }
}