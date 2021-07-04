<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Suite\Config;

use Container\Core\Config\ConfigType;
use Container\Core\Config\Json\JsonConfigParser;
use org\bovigo\vfs\vfsStream as TemporaryFileSystem;
use org\bovigo\vfs\vfsStreamFile as TemporaryFile;
use PHPUnit\Framework\TestCase;

class ConfigTypeTest extends TestCase {

    public function test_that_constructs_yaml(): void {
        // given/when/then
        $this->assertNotNull(ConfigType::YAML());
    }

    public function test_that_constructs_json(): void {
        // given/when/then
        $this->assertNotNull(ConfigType::JSON());
    }

    public function test_that_constructs_xml(): void {
        // given/when/then
        $this->assertNotNull(ConfigType::XML());
    }

    public function test_that_throws_exception_constructing_with_not_supported_value(): void {
        // given
        $not_supported_file_type = 'txt';

        // when/then
        $this->expectException(\InvalidArgumentException::class);
        new ConfigType($not_supported_file_type);
    }

    public function test_that_returns_supported_values(): void {
        // given
        $supported_values = [ 'yaml', 'json', 'xml' ];

        // when
        $file_type_values = ConfigType::getValues();

        // then
        $this->assertEquals($supported_values, $file_type_values);
    }

    public function test_that_constructs_from_file_extension(): void {
        // given
        $file_json = 'sample_file.json';

        // when
        $json = ConfigType::fromFileName($file_json);

        // when
        $this->assertEquals(ConfigType::JSON(), $json);
    }

    public function test_that_creates_file_parser(): void {
        // given
        $directory = TemporaryFileSystem::setup('config');
        $directory->addChild(new TemporaryFile($file_name = 'sample_file.json'));

        $json_file = $directory->getChild($file_name);
        $json_parser = new JsonConfigParser($json_file->url());

        // when
        $file_loader = ConfigType::JSON()->getParser($json_file->url());

        // then
        $this->assertEquals($json_parser, $file_loader);
    }

    public function test_that_equals_two_values(): void {
        // given
        $json = ConfigType::JSON();

        // when
        $is_equal = $json->equalsTo(ConfigType::JSON());
        $is_not_equal = $json->equalsTo(ConfigType::XML());

        // then
        $this->assertTrue($is_equal);
        $this->assertFalse($is_not_equal);
    }

    public function test_that_differs_two_values(): void {
        // given
        $json = ConfigType::JSON();

        // when
        $is_different = $json->differsFrom(ConfigType::XML());
        $is_not_different = $json->differsFrom(ConfigType::JSON());

        // then
        $this->assertTrue($is_different);
        $this->assertFalse($is_not_different);
    }

    public function test_that_casts_to_string(): void {
        // given
        $file_type = ConfigType::JSON();
        $file_type_raw = $file_type->getValue();

        // when
        $file_type_string = (string)$file_type;

        // then
        $this->assertEquals($file_type_raw, $file_type_string);
    }
}