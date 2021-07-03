<?php
declare(strict_types=1);

namespace Container\Core\Config\Xml;

use Container\Core\Config\ConfigParser;
use Container\Core\ContainerConfig;
use Container\Core\Dependency\Dependency;

final class XmlConfigParser implements ConfigParser {

    public function __construct(
        private string $file_name
    ) {
        if (!extension_loaded('xml')) {
            throw new XmlConfigParserException('PHP ext-xml is not loaded');
        }

        if (!is_file($file_name)) {
            throw new XmlConfigParserException("Config file '$file_name' not found");
        }
    }

    public function parse(): ContainerConfig {
        $config = $this->loadConfigFile();

        $container = new ContainerConfig();
        $container->setDependencies(
            $this->parseDependencies($config)
        );

        return $container->seal();
    }

    private function loadConfigFile(): \SimpleXMLElement {
        libxml_use_internal_errors(true);

        $config = simplexml_load_file($this->file_name);
        if ($config === false) {
            throw new XmlConfigParserException("Cannot load file '$this->file_name'");
        }

        return $config;
    }

    /**
     * @return Dependency[]
     */
    private function parseDependencies(\SimpleXMLElement $config): array {
        if (!isset($config->dependencies)) {
            return []; // not defined => nothing to parse
        }

        $dependencies = [];
        foreach ($config->dependencies->dependency ?? [] as $dependency) {
            if ($dependency === null) {
                continue; // if somehow happens
            }

            $dependencies[] = new Dependency(
                $this->getDependencyIsShared($dependency),
                $this->getDependencyAbstract($dependency),
                $this->getDependencyDefinition($dependency)
            );
        }

        return $dependencies;
    }

    private function getDependencyIsShared(\SimpleXMLElement $dependency): bool {
        $shared = $dependency->attributes()->shared ?? null;
        if ($shared === null) {
            return false; // default value => transient
        }

        $shared = filter_var($shared, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        if ($shared === null) {
            return false; // default => transient
        }

        return $shared;
    }

    private function getDependencyAbstract(\SimpleXMLElement $dependency): string {
        $abstract = $dependency->abstract ?? null;
        if ($abstract === null) {
            throw new XmlConfigParserException("Dependency property 'abstract' is not defined");
        }

        return (string)$abstract;
    }

    private function getDependencyDefinition(\SimpleXMLElement $dependency): ?string {
        $definition = $dependency->definition ?? null;
        if ($definition === null) {
            return null; // no definition => definition = abstract
        }

        return (string)$definition;
    }
}