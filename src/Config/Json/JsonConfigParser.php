<?php
declare(strict_types=1);

namespace Container\Core\Config\Json;

use Container\Core\Config\ConfigParser;
use Container\Core\ContainerConfig;
use Container\Core\Dependency\Dependency;

final class JsonConfigParser implements ConfigParser {

    public function __construct(
        private string $file_name
    ) {
        if (!extension_loaded('json')) {
            throw new JsonConfigParserException('PHP ext-json is not loaded');
        }

        if (!is_file($file_name)) {
            throw new JsonConfigParserException("Config file '$file_name' not found");
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

    private function loadConfigFile(): array {
        $contents = file_get_contents($this->file_name);
        if ($contents === false) {
            throw new JsonConfigParserException("Cannot load file '$this->file_name'");
        }

        $config = json_decode($contents, true);
        if ($config === null) {
            throw new JsonConfigParserException("Cannot decode file '$this->file_name'");
        }

        return $config;
    }

    /**
     * @return Dependency[]
     */
    private function parseDependencies(array $container): array {
        if (!array_key_exists('dependencies', $container)) {
            return []; // not defined => nothing to parse
        }

        $dependencies = [];
        foreach ($container['dependencies'] as $dependency) {
            try {
                $dependencies[] = new Dependency(
                    $dependency['shared'] ?? false,
                    $dependency['abstract'],
                    $dependency['definition'] ?? null
                );
            } catch (\InvalidArgumentException $e) {
                throw JsonConfigParserException::fromException($e);
            }
        }

        return $dependencies;
    }
}