<?php
declare(strict_types=1);

namespace Container\Core\Config;

use Container\Core\Config\Parser\ConfigParser;
use Container\Core\Config\Parser\Json\JsonConfigParser;
use Container\Core\Config\Parser\Xml\XmlConfigParser;
use Container\Core\Config\Parser\Yaml\YamlConfigParser;

final class ConfigType implements \Stringable {

    private const YAML = 'yaml';
    private const JSON = 'json';
    private const XML = 'xml';

    public function __construct(
        private string $value
    ) {
        if (!in_array($value, self::getValues(), true)) {
            throw new \InvalidArgumentException("Config type '$value' is not supported");
        }
    }

    public static function YAML(): self {
        return new self(self::YAML);
    }

    public static function JSON(): self {
        return new self(self::JSON);
    }

    public static function XML(): self {
        return new self(self::XML);
    }

    public static function fromFileName(string $file_name): ?self {
        return match (pathinfo($file_name, PATHINFO_EXTENSION)) {
            'yaml, yml' => self::YAML(),
            'json' => self::JSON(),
            'xml' => self::XML(),
            default => null
        };
    }

    public static function getValues(): array {
        $values = (new \ReflectionClass(self::class))
            ->getConstants(\ReflectionClassConstant::IS_PRIVATE);

        return array_values($values);
    }

    public function getValue(): string {
        return $this->value;
    }

    public function getParser(string $file_name): ConfigParser {
        return match ($this->getValue()) {
            self::YAML => new YamlConfigParser($file_name),
            self::JSON => new JsonConfigParser($file_name),
            self::XML => new XmlConfigParser($file_name),
        };
    }

    public function equalsTo(self $config_type): bool {
        return $config_type->getValue() === $this->getValue();
    }

    public function differsFrom(self $config_type): bool {
        return $config_type->getValue() !== $this->getValue();
    }

    public function __toString(): string {
        return $this->getValue();
    }
}