<?php
declare(strict_types=1);

namespace Container\Test\Unit\Suite\Dependency;

use Container\Core\ContainerException;
use Container\Core\Dependency\Dependency;
use Container\Core\Dependency\DependencyRegistry;
use Container\Test\Unit\Stub\ClassDependencyInterface;
use Container\Test\Unit\Stub\ClassWithoutDependency;
use PHPUnit\Framework\TestCase;

class DependencyRegistryTest extends TestCase {

    private DependencyRegistry $registry;

    protected function setUp(): void {
        $this->registry = new DependencyRegistry();
    }

    public function test_that_constructed_registry_is_empty(): void {
        // given
        $registry = new DependencyRegistry();

        // when/then
        $this->assertEmpty($registry);
    }

    public function test_that_makes_dependency_from_class(): void {
        // given/when
        $instance = $this->registry->make(ClassWithoutDependency::class);

        // then
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance);
    }

    public function test_that_makes_registered_transient_dependency(): void {
        // given
        $this->registry->add(
            Dependency::transient(ClassDependencyInterface::class, ClassWithoutDependency::class)
        );

        // when
        $instance_1 = $this->registry->make(ClassDependencyInterface::class);
        $instance_2 = $this->registry->make(ClassDependencyInterface::class);

        // then
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance_1);
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance_2);
        $this->assertNotSame($instance_1, $instance_2);
    }

    public function test_that_makes_registered_shared_dependency(): void {
        // given
        $this->registry->add(
            Dependency::shared(ClassDependencyInterface::class, ClassWithoutDependency::class)
        );

        // when
        $instance_1 = $this->registry->make(ClassDependencyInterface::class);
        $instance_2 = $this->registry->make(ClassDependencyInterface::class);

        // then
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance_1);
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance_2);
        $this->assertSame($instance_1, $instance_2);
    }

    public function test_that_gets_dependency(): void {
        // given
        $this->registry->add(
            $dependency = Dependency::shared(ClassWithoutDependency::class)
        );

        // when
        $returned_dependency = $this->registry->get(ClassWithoutDependency::class);

        // then
        $this->assertEquals($dependency, $returned_dependency);
    }

    public function test_that_throws_exception_trying_to_get_not_registered_dependency(): void {
        // when/then
        $this->expectException(ContainerException::class);
        $this->registry->get(ClassWithoutDependency::class);
    }

    public function test_that_adds_dependency(): void {
        // given
        $this->registry->add(Dependency::shared(ClassWithoutDependency::class));

        // when
        $is_registered = $this->registry->has(ClassWithoutDependency::class);

        // then
        $this->assertTrue($is_registered);
    }

    public function test_that_throws_exception_trying_to_add_existing_dependency(): void {
        // given
        $this->registry->add(
            $dependency = Dependency::shared(ClassWithoutDependency::class)
        );

        // when/then
        $this->expectException(ContainerException::class);
        $this->registry->add($dependency);
    }

    public function test_that_removes_dependency(): void {
        // given
        $this->registry->add(Dependency::shared(ClassWithoutDependency::class));

        // then - pre remove
        $this->assertTrue($this->registry->has(ClassWithoutDependency::class));

        // when
        $this->registry->remove(ClassWithoutDependency::class);

        // then - post remove
        $this->assertFalse($this->registry->has(ClassWithoutDependency::class));
    }

    public function test_that_throws_exception_trying_to_remove_not_existing_dependency(): void {
        // when/then
        $this->expectException(ContainerException::class);
        $this->registry->remove(ClassWithoutDependency::class);
    }
}