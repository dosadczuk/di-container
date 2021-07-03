<?php
declare(strict_types=1);

namespace Container\Core\Config;

use Container\Core\Config\Json\JsonConfigParser;
use Container\Core\Config\Xml\XmlConfigParser;

final class ConfigType implements \Stringable {

    private const JSON = 'json';
    private const XML = 'xml';

    private string $value;

    public function __construct(string $value) {
        if (!in_array($value, self::getValues(), true)) {
            throw new \InvalidArgumentException("Config type '$value' is not supported");
        }

        $this->value = $value;
    }

    public static function JSON(): self {
        return new self(self::JSON);
    }

    public static function XML(): self {
        return new self(self::XML);
    }

    public static function fromFile(string $file_name): ?self {
        return match (pathinfo($file_name, PATHINFO_EXTENSION)) {
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