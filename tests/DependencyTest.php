<?php
declare(strict_types=1);

namespace Foundation\Tests;

use Foundation\Container\Dependency;
use PHPUnit\Framework\TestCase;

class DependencyTest extends TestCase {

    public function test_that_newly_created_dependency_has_no_instance(): void {
        $dependency = Dependency::normal('test');
        self::assertFalse($dependency->isInstantiated());
    }

    public function test_that_instance_can_be_assigned_to_previously_created_dependency(): void {
        $dependency = Dependency::normal('test');
        self::assertFalse($dependency->isInstantiated());

        $dependency->setInstance(new \stdClass());
        self::assertTrue($dependency->isInstantiated());
    }

    public function test_that_abstract_is_definition_when_creating_dependency_with_no_definition(): void {
        $dependency = Dependency::normal('test');
        self::assertEquals('test', $dependency->getAbstract());
        self::assertEquals('test', $dependency->getDefinition());
    }

    public function test_that_dependency_can_be_created_with_abstract_and_definition(): void {
        $dependency = Dependency::normal('test', 'test2');
        self::assertEquals('test', $dependency->getAbstract());
        self::assertEquals('test2', $dependency->getDefinition());
    }
}