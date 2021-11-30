<?php
declare(strict_types=1);

namespace Container\Test\Unit\Suite\Resolver;

use Container\Core\Resolver\ClassResolver;
use Container\Core\Resolver\ResolverException;
use Container\Test\Unit\Stub\ClassOneWithClassTwoConstructorDependency;
use Container\Test\Unit\Stub\ClassWithBuiltinTypedConstructorDependency;
use Container\Test\Unit\Stub\ClassWithBuiltinTypedPropertyDependency;
use Container\Test\Unit\Stub\ClassWithConstructorDependency;
use Container\Test\Unit\Stub\ClassWithNestedDependencies;
use Container\Test\Unit\Stub\ClassWithNonTypedConstructorDependency;
use Container\Test\Unit\Stub\ClassWithNonTypedPropertyDependency;
use Container\Test\Unit\Stub\ClassWithoutDependency;
use Container\Test\Unit\Stub\ClassWithPropertyDependency;
use Container\Test\Unit\Stub\ClassWithSelfConstructorDependency;
use Container\Test\Unit\Stub\ClassWithSetterDependency;
use Container\Test\Unit\Stub\ClassWithUnionTypedConstructorDependency;
use Container\Test\Unit\Stub\ClassWithUnionTypedPropertyDependency;
use PHPUnit\Framework\TestCase;

class ClassDependencyResolverTest extends TestCase {

    public function test_that_resolves_class_without_dependencies(): void {
        // given
        $resolver = new ClassResolver(ClassWithoutDependency::class);

        // when
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance);
    }

    public function test_that_resolves_class_with_constructor_dependency(): void {
        // given
        $resolver = new ClassResolver(ClassWithConstructorDependency::class);

        // when
        /** @var ClassWithConstructorDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithConstructorDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_class_with_property_dependency(): void {
        // given
        $resolver = new ClassResolver(ClassWithPropertyDependency::class);

        // when
        /** @var ClassWithPropertyDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithPropertyDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_class_with_setter_dependency(): void {
        // given
        $resolver = new ClassResolver(ClassWithSetterDependency::class);

        // when
        /** @var ClassWithSetterDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithSetterDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_class_with_nested_dependencies(): void {
        // given
        $resolver = new ClassResolver(ClassWithNestedDependencies::class);

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
        $resolver = new ClassResolver(ClassWithNonTypedPropertyDependency::class);

        // when/then
        $this->expectException(ResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_property_with_builtin_type(): void {
        // given
        $resolver = new ClassResolver(ClassWithBuiltinTypedPropertyDependency::class);

        // when/then
        $this->expectException(ResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_property_with_union_type(): void {
        // given
        $resolver = new ClassResolver(ClassWithUnionTypedPropertyDependency::class);

        // when/then
        $this->expectException(ResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_without_type(): void {
        // given
        $resolver = new ClassResolver(ClassWithNonTypedConstructorDependency::class);

        // when/then
        $this->expectException(ResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_builtin_type(): void {
        // given
        $resolver = new ClassResolver(ClassWithBuiltinTypedConstructorDependency::class);

        // when/then
        $this->expectException(ResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_union_type(): void {
        // given
        $resolver = new ClassResolver(ClassWithUnionTypedConstructorDependency::class);

        // when/then
        $this->expectException(ResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_resolving_class_with_self_as_constructor_dependency(): void {
        // given
        $resolver = new ClassResolver(ClassWithSelfConstructorDependency::class);

        // when/then
        $this->expectException(ResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_resolving_class_with_cycling_dependency(): void {
        // given
        $resolver = new ClassResolver(ClassOneWithClassTwoConstructorDependency::class);

        // when/then
        $this->expectException(ResolverException::class);
        $resolver->resolve();
    }
}