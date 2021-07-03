<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Suite\Config;

use Container\Core\Config\ConfigParserException;
use Container\Core\Config\ConfigParserFactory;
use Container\Core\Config\ConfigType;
use Container\Core\Config\Json\JsonConfigParser;
use org\bovigo\vfs\vfsStream as TemporaryFileSystem;
use org\bovigo\vfs\vfsStreamDirectory as TemporaryDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class ConfigParserFactoryTest extends TestCase {

    private ConfigParserFactory $factory;

    protected TemporaryDirectory $directory;

    protected function setUp(): void {
        $this->factory = new ConfigParserFactory();
        $this->directory = TemporaryFileSystem::setup('config');
    }

    public function test_that_creates_parser_for_file_name_with_extension(): void {
        // given
        $this->addTempFile($file_name = 'sample_config.json');
        $file_path = $this->getTempFilePath($file_name);

        // when
        $config_parser = $this->factory->createParser($file_path);

        // then
        $this->assertInstanceOf(JsonConfigParser::class, $config_parser);
    }

    public function test_that_creates_parser_for_file_name_without_extension_but_with_given_config_type(): void {
        // given
        $this->addTempFile($file_name = 'sample_config.config');
        $file_path = $this->getTempFilePath($file_name);

        // when
        $config_parser = $this->factory->createParser($file_path, ConfigType::JSON());

        // then
        $this->assertInstanceOf(JsonConfigParser::class, $config_parser);
    }

    public function test_that_throws_exception_creating_parser_from_file_with_no_or_unsupported_extension(): void {
        // given
        $this->addTempFile($file_name = 'sample_config.config');
        $file_path = $this->getTempFilePath($file_name);

        // when/then
        $this->expectException(ConfigParserException::class);
        $this->factory->createParser($file_path);
    }

    private function addTempFile(string $name): void {
        $this->directory->addChild(new vfsStreamFile($name));
    }

    private function getTempFilePath(string $name): string {
        return $this->directory->getChild($name)->url();
    }
}