<?php
declare(strict_types=1);

namespace Container\Core\Config;

use Container\Core\ContainerConfig;

final class ConfigCreator {

    public static function createFromFileName(string $file_name, ?ConfigType $type = null): ContainerConfig {
        if (($type ??= ConfigType::fromFileName($file_name)) === null) {
            throw new ConfigCreatorException("Cannot determine config type for file '$file_name'");
        }

        return $type->getParser($file_name)->parse();
    }
}