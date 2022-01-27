<?php
declare(strict_types=1);

namespace Container\Core;

use Container\Core\Parser\ConfigParser;
use Container\Core\Parser\JsonConfigParser;
use Container\Core\Parser\XmlConfigParser;
use Container\Core\Parser\YamlConfigParser;

/**
 * @internal
 */
enum ConfigType: string {

    case YAML = 'yaml';
    case JSON = 'json';
    case XML = 'xml';

    public static function fromFileName(string $file_name): ?self {
        return match (pathinfo($file_name, PATHINFO_EXTENSION)) {
            'yaml', 'yml' => self::YAML,
            'json'        => self::JSON,
            'xml'         => self::XML,
            default       => null
        };
    }

    public static function getValues(): array {
        return array_map(fn(self $enum) => $enum->value, self::cases());
    }

    public function getParser(string $file_name): ConfigParser {
        return match ($this) {
            self::YAML => new YamlConfigParser($file_name),
            self::JSON => new JsonConfigParser($file_name),
            self::XML  => new XmlConfigParser($file_name),
        };
    }
}