<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Suite\Dependency\Resolver;

use Container\Core\Dependency\Resolver\ClassDependencyResolver;
use Container\Core\Dependency\Resolver\DependencyResolverException;
use Container\Tests\Unit\Stub\ClassWithBuiltinTypedConstructorDependencyAndWithDefaultValue;
use Container\Tests\Unit\Stub\ClassWithBuiltinTypedConstructorDependencyAndWithoutDefaultValue;
use Container\Tests\Unit\Stub\ClassWithBuiltinTypedPropertyDependency;
use Container\Tests\Unit\Stub\ClassWithConstructorDependency;
use Container\Tests\Unit\Stub\ClassWithNestedDependencies;
use Container\Tests\Unit\Stub\ClassWithNonTypedConstructorDependency;
use Container\Tests\Unit\Stub\ClassWithNonTypedPropertyDependency;
use Container\Tests\Unit\Stub\ClassWithoutDependency;
use Container\Tests\Unit\Stub\ClassWithPropertyDependency;
use Container\Tests\Unit\Stub\ClassWithSetterDependency;
use Container\Tests\Unit\Stub\ClassWithUnionTypedConstructorDependency;
use Container\Tests\Unit\Stub\ClassWithUnionTypedPropertyDependency;
use PHPUnit\Framework\TestCase;

class ClassDependencyResolverTest extends TestCase {

    public function test_that_resolves_class_without_dependencies(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithoutDependency::class);

        // when
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance);
    }

    public function test_that_resolves_class_with_constructor_dependency(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithConstructorDependency::class);

        // when
        /** @var ClassWithConstructorDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithConstructorDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_class_with_property_dependency(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithPropertyDependency::class);

        // when
        /** @var ClassWithPropertyDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithPropertyDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_class_with_setter_dependency(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithSetterDependency::class);

        // when
        /** @var ClassWithSetterDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithSetterDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_class_with_nested_dependencies(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithNestedDependencies::class);

        // when
        /** @var ClassWithNestedDependencies $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithNestedDependencies::class, $instance);
        $this->assertNotNull($instance->getDependencyWithConstructor());
        $this->assertNotNull($instance->getDependencyWithConstructor()->getDependency());
        $this->assertNotNull($instance->getDependencyWithProperty());
        $this->assertNotNull($instance->getDependencyWithProperty()->getDependency());
        $this->assertNotNull($instance->getDependencyWithSetter());
        $this->assertNotNull($instance->getDependencyWithSetter()->getDependency());
    }

    public function test_that_throws_exception_trying_to_resolve_property_without_type(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithNonTypedPropertyDependency::class);

        // when/then
        $this->expectException(DependencyResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_property_with_builtin_type(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithBuiltinTypedPropertyDependency::class);

        // when/then
        $this->expectException(DependencyResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_property_with_union_type(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithUnionTypedPropertyDependency::class);

        // when/then
        $this->expectException(DependencyResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_without_type(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithNonTypedConstructorDependency::class);

        // when/then
        $this->expectException(DependencyResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_builtin_type_and_without_default_value(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithBuiltinTypedConstructorDependencyAndWithoutDefaultValue::class);

        // when/then
        $this->expectException(DependencyResolverException::class);
        $resolver->resolve();
    }

    public function test_that_resolves_class_with_constructor_builtin_dependency_with_default_value(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithBuiltinTypedConstructorDependencyAndWithDefaultValue::class);

        // when
        $dependency = $resolver->resolve();

        // then
        $this->assertNotEmpty($dependency);
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_union_type(): void {
        // given
        $resolver = new ClassDependencyResolver(ClassWithUnionTypedConstructorDependency::class);

        // when/then
        $this->expectException(DependencyResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_resolving_class_with_cycling_dependencies(): void {
        $this->markTestSkipped('Feature still to add');
    }
}