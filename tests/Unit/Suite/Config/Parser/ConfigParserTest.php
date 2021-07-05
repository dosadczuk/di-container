<?php
declare(strict_types=1);

namespace Container\Test\Unit\Suite\Config\Parser;

use org\bovigo\vfs\vfsStream as TemporaryFileSystem;
use org\bovigo\vfs\vfsStreamDirectory as TemporaryDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

abstract class ConfigParserTest extends TestCase {

    protected TemporaryDirectory $directory;

    protected function setUp(): void {
        $this->directory = TemporaryFileSystem::setup('config', null, [
            $this->getConfigName() => $this->getConfigContent(),
        ]);
    }

    abstract protected function getConfigName(): string;

    abstract protected function getConfigContent(): string;

    protected function getConfigPath(string $file_name): string {
        return $this->directory->getChild($file_name)->url();
    }

    protected function addTempFile(string $name, string $content): void {
        $this->directory->addChild(
            (new vfsStreamFile($name))->withContent($content)
        );
    }
}