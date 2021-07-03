<?php
declare(strict_types=1);

namespace Container\Core\Config;


final class ConfigParserFactory {

    public function createParser(string $file_name, ?ConfigType $config_type = null): ConfigParser {
        if (($config_type ??= ConfigType::fromFile($file_name)) === null) {
            throw new ConfigParserException("Cannot determine config type for file '$file_name'");
        }

        return $config_type->getParser($file_name);
    }
}