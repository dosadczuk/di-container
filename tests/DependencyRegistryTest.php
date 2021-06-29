<?php
declare(strict_types=1);

namespace Foundation\Tests;

use Foundation\Container\ContainerException;
use Foundation\Container\Dependency;
use Foundation\Container\DependencyRegistry;
use PHPUnit\Framework\TestCase;

class DependencyRegistryTest extends TestCase {

    public function test_that_created_registry_has_no_entries(): void {
        $registry = new DependencyRegistry();
        self::assertEmpty($registry);
    }

    public function test_that_can_get_registered_dependency(): void {
        $registry = new DependencyRegistry();
        $registry->add($dependency = Dependency::normal('test'));

        $returned = $registry->get('test');
        self::assertEquals($dependency, $returned);
    }

    public function test_that_getting_not_registered_dependency_throws_exception(): void {
        $registry = new DependencyRegistry();

        self::expectException(ContainerException::class);
        $registry->get('test');
    }

    public function test_that_can_add_new_dependency(): void {
        $registry = new DependencyRegistry();
        $registry->add(Dependency::normal('test'));
        self::assertTrue($registry->has('test'));
    }

    public function test_that_adding_same_dependency_throws_exception(): void {
        $dependency = Dependency::normal('test');
        $registry = new DependencyRegistry();
        $registry->add($dependency);
        self::expectException(ContainerException::class);
        $registry->add($dependency);
    }

    public function test_that_can_remove_registered_dependency(): void {
        $registry = new DependencyRegistry();
        $registry->add(Dependency::normal('test'));
        self::assertTrue($registry->has('test'));
        $registry->remove('test');
        self::assertFalse($registry->has('test'));
    }

    public function test_that_removing_not_registered_dependency_throws_exception(): void {
        $registry = new DependencyRegistry();
        self::expectException(ContainerException::class);
        $registry->remove('test');
    }
}