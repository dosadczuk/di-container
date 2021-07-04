<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Suite\Config\Parser;

use Container\Core\Config\Parser\Yaml\YamlConfigParser;
use Container\Core\Config\Parser\Yaml\YamlConfigParserException;
use Container\Core\Dependency\Dependency;
use Container\Tests\Unit\Stub\ClassDependencyInterface;
use Container\Tests\Unit\Stub\ClassWithNestedDependencies;
use Container\Tests\Unit\Stub\ClassWithoutDependency;

class YamlConfigParserTest extends ConfigParserTest {

    private Dependency $dependency_1;

    private Dependency $dependency_2;

    protected function setUp(): void {
        parent::setUp();

        $this->dependency_1 = Dependency::transient(
            ClassDependencyInterface::class,
            ClassWithoutDependency::class
        );
        $this->dependency_2 = Dependency::shared(
            ClassDependencyInterface::class,
            ClassWithNestedDependencies::class
        );
    }

    public function test_that_parses_dependencies_from_file(): void {
        // given
        $file_path = $this->getConfigPath($this->getConfigName());
        $config_parser = new YamlConfigParser($file_path);

        // when
        $config = $config_parser->parse();

        // then
        $this->assertCount(2, $config->dependencies);
        $this->assertEquals($this->dependency_1, $config->dependencies[0]);
        $this->assertEquals($this->dependency_2, $config->dependencies[1]);
    }

    public function test_that_throws_exception_parsing_not_existing_file(): void {
        // given
        $not_existing_file = 'sample_file.yaml';

        // when/then
        $this->expectException(YamlConfigParserException::class);
        new YamlConfigParser($not_existing_file);
    }

    public function test_that_throws_exception_parsing_invalid_yaml_file(): void {
        // given
        $this->addTempFile('sample_file.txt', 'sample text');
        $config_parser = new YamlConfigParser($this->getConfigPath('sample_file.txt'));

        // when/then
        $this->expectException(YamlConfigParserException::class);
        $config_parser->parse();
    }

    protected function getConfigName(): string {
        return 'dependencies.yaml';
    }

    protected function getConfigContent(): string {
        return <<<YAML
---
dependencies:
  - shared: false
    abstract: Container\Tests\Unit\Stub\ClassDependencyInterface
    definition: Container\Tests\Unit\Stub\ClassWithoutDependency

  - shared: true
    abstract: Container\Tests\Unit\Stub\ClassDependencyInterface
    definition: Container\Tests\Unit\Stub\ClassWithNestedDependencies
YAML;
    }
}