<?php
declare(strict_types=1);

namespace Container\Test\Unit\Suite\Config\Parser;

use Container\Core\Config\Parser\ConfigParserException;
use Container\Core\Config\Parser\JsonConfigParser;
use Container\Core\Dependency\Dependency;
use Container\Test\Unit\Stub\ClassDependencyInterface;
use Container\Test\Unit\Stub\ClassWithNestedDependencies;
use Container\Test\Unit\Stub\ClassWithoutDependency;

class JsonConfigParserTest extends ConfigParserTest {

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
        $config_parser = new JsonConfigParser($file_path);

        // when
        $config = $config_parser->parse();

        // then
        $this->assertCount(2, $config->dependencies);
        $this->assertEquals($this->dependency_1, $config->dependencies[0]);
        $this->assertEquals($this->dependency_2, $config->dependencies[1]);
    }

    public function test_that_throws_exception_parsing_not_existing_file(): void {
        // given
        $not_existing_file = 'sample_file.json';

        // when/then
        $this->expectException(ConfigParserException::class);
        new JsonConfigParser($not_existing_file);
    }

    public function test_that_throws_exception_parsing_invalid_json_file(): void {
        // given
        $this->addTempFile('sample_file.txt', 'sample text');
        $config_parser = new JsonConfigParser($this->getConfigPath('sample_file.txt'));

        // when/then
        $this->expectException(ConfigParserException::class);
        $config_parser->parse();
    }

    protected function getConfigName(): string {
        return 'dependencies.json';
    }

    protected function getConfigContent(): string {
        return <<<JSON
{
  "dependencies": [
    {
      "abstract": "Container\\\Test\\\Unit\\\Stub\\\ClassDependencyInterface",
      "definition": "Container\\\Test\\\Unit\\\Stub\\\ClassWithoutDependency"
    },
    {
      "shared": true,
      "abstract": "Container\\\Test\\\Unit\\\Stub\\\ClassDependencyInterface",
      "definition": "Container\\\Test\\\Unit\\\Stub\\\ClassWithNestedDependencies"
    }
  ]
}
JSON;
    }
}