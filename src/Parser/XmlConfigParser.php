<?php
declare(strict_types=1);

namespace Container\Core\Parser;

use Container\Core\Config;
use Container\Core\Dependency;

/**
 * @internal
 */
final class XmlConfigParser implements ConfigParser {

    public function __construct(
        private string $file_name
    ) {
        if (!extension_loaded('xml')) {
            throw new ConfigParserException('PHP ext-xml is not loaded');
        }

        if (!is_file($file_name)) {
            throw new ConfigParserException("Config file '$file_name' not found");
        }
    }

    public function parse(): Config {
        $structure = $this->loadConfigFile();

        $config = new Config();
        $config->dependencies = $this->parseDependencies($structure);

        return $config->seal();
    }

    private function loadConfigFile(): \SimpleXMLElement {
        libxml_use_internal_errors(true);

        $config = simplexml_load_string(file_get_contents($this->file_name));
        if ($config === false) {
            throw new ConfigParserException("Cannot load file '$this->file_name'");
        }

        return $config;
    }

    /**
     * @return Dependency[]
     */
    private function parseDependencies(\SimpleXMLElement $config): array {
        if (!isset($config->dependencies)) {
            return []; // nothing to parse
        }

        $dependencies = [];
        foreach ($config->dependencies->dependency as $dependency) {
            if ($dependency === null) {
                continue; // if somehow happens
            }

            try {
                $dependencies[] = new Dependency(
                    $this->getDependencyIsShared($dependency),
                    $this->getDependencyAbstract($dependency),
                    $this->getDependencyDefinition($dependency)
                );
            } catch (\InvalidArgumentException $e) {
                throw ConfigParserException::fromException($e);
            }
        }

        return $dependencies;
    }

    private function getDependencyIsShared(\SimpleXMLElement $dependency): bool {
        $shared = $dependency->attributes()->shared ?? null;
        if ($shared === null) {
            return false; // transient
        }

        $is_shared = filter_var($shared, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        if ($is_shared === null) {
            throw new ConfigParserException("Dependency attribute 'shared' is not valid bool value");
        }

        return $is_shared;
    }

    private function getDependencyAbstract(\SimpleXMLElement $dependency): string {
        $abstract = $dependency->abstract ?? null;
        if ($abstract === null) {
            throw new ConfigParserException("Dependency property 'abstract' is not defined");
        }

        return (string)$abstract;
    }

    private function getDependencyDefinition(\SimpleXMLElement $dependency): ?string {
        $definition = $dependency->definition ?? null;
        if ($definition === null) {
            return null;
        }

        return (string)$definition;
    }
}