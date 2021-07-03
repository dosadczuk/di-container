<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Suite\Dependency;

use Container\Core\Dependency\Dependency;
use Container\Tests\Unit\Stub\ClassDependencyInterface;
use Container\Tests\Unit\Stub\ClassWithoutDependency;
use PHPUnit\Framework\TestCase;

class DependencyTest extends TestCase {

    public function test_that_constructs_transient_dependency(): void {
        // given/when
        $dependency = Dependency::transient(ClassWithoutDependency::class);

        // then
        $this->assertFalse($dependency->isShared());
    }

    public function test_that_constructs_shared_dependency(): void {
        // given/when
        $dependency = Dependency::shared(ClassWithoutDependency::class);

        // then
        $this->assertTrue($dependency->isShared());
    }

    public function test_that_constructed_dependency_has_no_instance(): void {
        // given/when
        $dependency = Dependency::shared(ClassWithoutDependency::class);

        // then
        $this->assertFalse($dependency->isInstantiated());
    }

    public function test_that_assigns_instance_to_dependency(): void {
        //given
        $dependency = Dependency::shared(ClassWithoutDependency::class);

        // then - pre instance
        $this->assertFalse($dependency->isInstantiated());

        // when
        $dependency->setInstance(new \stdClass());

        // then - post instance
        $this->assertTrue($dependency->isInstantiated());
    }

    public function test_that_abstract_is_used_as_definition_when_constructing_dependency_without_definition(): void {
        // given/when
        $dependency = Dependency::shared(ClassWithoutDependency::class);

        // then
        $this->assertEquals($dependency->getAbstract(), $dependency->getDefinition());
    }

    public function test_that_constructs_dependency_with_definition(): void {
        // given/when
        $dependency = Dependency::shared(ClassDependencyInterface::class, ClassWithoutDependency::class);

        // then
        $this->assertEquals(ClassDependencyInterface::class, $dependency->getAbstract());
        $this->assertEquals(ClassWithoutDependency::class, $dependency->getDefinition());
    }

    public function test_that_throws_exception_trying_construct_dependency_with_not_existing_interface_or_class_as_abstract(): void {
        // given
        $not_existing_interface_nor_class = 'test_sample_class';

        // when/then
        $this->expectException(\InvalidArgumentException::class);
        Dependency::shared($not_existing_interface_nor_class);
    }

    public function test_that_throws_exception_trying_construct_dependency_with_not_existing_class_as_definition(): void {
        // given
        $not_existing_class = 'test_sample_class';

        // when/then
        $this->expectException(\InvalidArgumentException::class);
        Dependency::shared(ClassDependencyInterface::class, $not_existing_class);
    }
}