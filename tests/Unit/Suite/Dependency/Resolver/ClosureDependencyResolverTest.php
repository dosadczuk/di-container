<?php
declare(strict_types=1);

namespace Container\Test\Unit\Suite\Dependency\Resolver;

use Container\Core\Dependency\Resolver\ClosureDependencyResolver;
use Container\Core\Dependency\Resolver\DependencyResolverException;
use Container\Test\Unit\Stub\ClassWithBuiltinTypedConstructorDependency;
use Container\Test\Unit\Stub\ClassWithConstructorDependency;
use Container\Test\Unit\Stub\ClassWithNestedDependencies;
use Container\Test\Unit\Stub\ClassWithoutDependency;
use Container\Test\Unit\Stub\ClassWithPropertyDependency;
use Container\Test\Unit\Stub\ClassWithSetterDependency;
use PHPUnit\Framework\TestCase;

class ClosureDependencyResolverTest extends TestCase {

    public function test_that_resolves_closure_without_parameters(): void {
        // given
        $resolver = new ClosureDependencyResolver(fn() => new ClassWithoutDependency());

        // when
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance);
    }

    public function test_that_resolves_closure_with_parameter_of_class_without_dependencies(): void {
        // given
        $resolver = new ClosureDependencyResolver(fn(ClassWithoutDependency $dependency) => $dependency);

        // when
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance);
    }

    public function test_that_resolves_closure_with_parameter_of_class_with_constructor_dependency(): void {
        // given
        $resolver = new ClosureDependencyResolver(fn(ClassWithConstructorDependency $dependency) => $dependency);

        // when
        /** @var ClassWithConstructorDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithConstructorDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_closure_with_parameter_of_class_with_property_dependency(): void {
        // given
        $resolver = new ClosureDependencyResolver(fn(ClassWithPropertyDependency $dependency) => $dependency);

        // when
        /** @var ClassWithPropertyDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithPropertyDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_closure_with_parameter_of_class_with_setter_dependency(): void {
        // given
        $resolver = new ClosureDependencyResolver(fn(ClassWithSetterDependency $dependency) => $dependency);

        // when
        /** @var ClassWithSetterDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithSetterDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_closure_with_parameter_of_class_with_nested_dependencies(): void {
        // given
        $resolver = new ClosureDependencyResolver(fn(ClassWithNestedDependencies $dependency) => $dependency);

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

    public function test_that_throws_exception_trying_to_resolve_parameter_without_type(): void {
        // given
        $resolver = new ClosureDependencyResolver(fn($dependency) => $dependency);

        // when/then
        $this->expectException(DependencyResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_builtin_type(): void {
        // given
        $resolver = new ClosureDependencyResolver(function (string $dependency) {
            return new ClassWithBuiltinTypedConstructorDependency($dependency);
        });

        // when/then
        $this->expectException(DependencyResolverException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_union_type(): void {
        // given
        $resolver = new ClosureDependencyResolver(fn(string|int $dependency) => $dependency);

        // when/then
        $this->expectException(DependencyResolverException::class);
        $resolver->resolve();
    }
}