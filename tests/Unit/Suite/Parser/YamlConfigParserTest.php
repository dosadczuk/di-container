<?php
declare(strict_types=1);

namespace Container\Test\Unit\Suite\Parser;

use Container\Core\Dependency;
use Container\Core\Parser\ConfigParserException;
use Container\Core\Parser\YamlConfigParser;
use Container\Test\Unit\Stub\ClassDependencyInterface;
use Container\Test\Unit\Stub\ClassWithNestedDependencies;
use Container\Test\Unit\Stub\ClassWithoutDependency;
use org\bovigo\vfs\vfsStream as TemporaryFileSystem;
use org\bovigo\vfs\vfsStreamDirectory as TemporaryDirectory;
use org\bovigo\vfs\vfsStreamFile as TemporaryFile;
use PHPUnit\Framework\TestCase;

class YamlConfigParserTest extends TestCase {

    private TemporaryDirectory $directory;

    private Dependency $dependency_1;

    private Dependency $dependency_2;

    protected function setUp(): void {
        $this->directory = TemporaryFileSystem::setup('config', null, [
            'dependencies.yaml' => <<<YAML
---
dependencies:
  - shared: false
    abstract: Container\Test\Unit\Stub\ClassDependencyInterface
    definition: Container\Test\Unit\Stub\ClassWithoutDependency

  - shared: true
    abstract: Container\Test\Unit\Stub\ClassDependencyInterface
    definition: Container\Test\Unit\Stub\ClassWithNestedDependencies
YAML
            ,
        ]);

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
        $config_parser = new YamlConfigParser($this->getFilePath('dependencies.yaml'));

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
        $this->expectException(ConfigParserException::class);
        new YamlConfigParser($not_existing_file);
    }

    public function test_that_throws_exception_parsing_invalid_yaml_file(): void {
        // given
        $file_path = $this->addFileToDirectory('sample_file.txt', 'sample text');
        $config_parser = new YamlConfigParser($file_path);

        // when/then
        $this->expectException(ConfigParserException::class);
        $config_parser->parse();
    }

    private function addFileToDirectory(string $file_name, string $file_content = null): string {
        $this->directory->addChild(
            (new TemporaryFile($file_name))->withContent($file_content)
        );

        return $this->directory->getChild($file_name)->url();
    }

    private function getFilePath(string $file_name): string {
        return $this->directory->getChild($file_name)->url();
    }
}