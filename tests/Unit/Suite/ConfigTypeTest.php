<?php
declare(strict_types=1);

namespace Container\Test\Unit\Suite;

use Container\Core\ConfigType;
use Container\Core\Parser\JsonConfigParser;
use Container\Core\Parser\XmlConfigParser;
use Container\Core\Parser\YamlConfigParser;
use org\bovigo\vfs\vfsStream as TemporaryFileSystem;
use org\bovigo\vfs\vfsStreamFile as TemporaryFile;
use PHPUnit\Framework\TestCase;

class ConfigTypeTest extends TestCase {

    public function test_that_constructs_yaml_from_file_extension(): void {
        // given
        $file_yaml = 'sample_file.yaml';

        // when
        $yaml = ConfigType::fromFileName($file_yaml);

        // when
        $this->assertEquals(ConfigType::YAML, $yaml);
    }

    public function test_that_creates_yaml_config_parser(): void {
        // given
        $directory = TemporaryFileSystem::setup('config');
        $directory->addChild(new TemporaryFile($file_name = 'sample_file.yaml'));

        $yaml_file = $directory->getChild($file_name);
        $yaml_parser = new YamlConfigParser($yaml_file->url());

        // when
        $file_loader = ConfigType::YAML->getParser($yaml_file->url());

        // then
        $this->assertEquals($yaml_parser, $file_loader);
    }

    public function test_that_constructs_json_from_file_extension(): void {
        // given
        $file_json = 'sample_file.json';

        // when
        $json = ConfigType::fromFileName($file_json);

        // when
        $this->assertEquals(ConfigType::JSON, $json);
    }

    public function test_that_creates_json_config_parser(): void {
        // given
        $directory = TemporaryFileSystem::setup('config');
        $directory->addChild(new TemporaryFile($file_name = 'sample_file.json'));

        $json_file = $directory->getChild($file_name);
        $json_parser = new JsonConfigParser($json_file->url());

        // when
        $file_loader = ConfigType::JSON->getParser($json_file->url());

        // then
        $this->assertEquals($json_parser, $file_loader);
    }

    public function test_that_constructs_xml_from_file_extension(): void {
        // given
        $file_xml = 'sample_file.xml';

        // when
        $xml = ConfigType::fromFileName($file_xml);

        // when
        $this->assertEquals(ConfigType::XML, $xml);
    }

    public function test_that_creates_xml_config_parser(): void {
        // given
        $directory = TemporaryFileSystem::setup('config');
        $directory->addChild(new TemporaryFile($file_name = 'sample_file.xml'));

        $xml_file = $directory->getChild($file_name);
        $xml_parser = new XmlConfigParser($xml_file->url());

        // when
        $file_loader = ConfigType::XML->getParser($xml_file->url());

        // then
        $this->assertEquals($xml_parser, $file_loader);
    }

    public function test_that_returns_supported_values(): void {
        // given
        $supported_values = [ 'yaml', 'json', 'xml' ];

        // when
        $file_type_values = ConfigType::getValues();

        // then
        $this->assertEquals($supported_values, $file_type_values);
    }
}