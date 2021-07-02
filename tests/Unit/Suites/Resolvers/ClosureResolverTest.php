<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Suites\Resolvers;

use Container\Core\Resolvers\ClosureResolver;
use Container\Core\Resolvers\Exceptions\ParameterNotTypedException;
use Container\Core\Resolvers\Exceptions\ParameterWithBuiltinTypeException;
use Container\Core\Resolvers\Exceptions\ParameterWithUnionTypeException;
use Container\Tests\Unit\Stubs\ClassWithBuiltinTypedConstructorDependencyAndWithoutDefaultValue;
use Container\Tests\Unit\Stubs\ClassWithConstructorDependency;
use Container\Tests\Unit\Stubs\ClassWithNestedDependencies;
use Container\Tests\Unit\Stubs\ClassWithoutDependency;
use Container\Tests\Unit\Stubs\ClassWithPropertyDependency;
use Container\Tests\Unit\Stubs\ClassWithSetterDependency;
use PHPUnit\Framework\TestCase;

class ClosureResolverTest extends TestCase {

    public function test_that_resolves_closure_without_parameters(): void {
        // given
        $resolver = new ClosureResolver(fn() => new ClassWithoutDependency());

        // when
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance);
    }

    public function test_that_resolves_closure_with_parameter_of_class_without_dependencies(): void {
        // given
        $resolver = new ClosureResolver(fn(ClassWithoutDependency $dependency) => $dependency);

        // when
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithoutDependency::class, $instance);
    }

    public function test_that_resolves_closure_with_parameter_of_class_with_constructor_dependency(): void {
        // given
        $resolver = new ClosureResolver(fn(ClassWithConstructorDependency $dependency) => $dependency);

        // when
        /** @var ClassWithConstructorDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithConstructorDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_closure_with_parameter_of_class_with_property_dependency(): void {
        // given
        $resolver = new ClosureResolver(fn(ClassWithPropertyDependency $dependency) => $dependency);

        // when
        /** @var ClassWithPropertyDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithPropertyDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_closure_with_parameter_of_class_with_setter_dependency(): void {
        // given
        $resolver = new ClosureResolver(fn(ClassWithSetterDependency $dependency) => $dependency);

        // when
        /** @var ClassWithSetterDependency $instance */
        $instance = $resolver->resolve();

        // then
        $this->assertInstanceOf(ClassWithSetterDependency::class, $instance);
        $this->assertNotNull($instance->getDependency());
    }

    public function test_that_resolves_closure_with_parameter_of_class_with_nested_dependencies(): void {
        // given
        $resolver = new ClosureResolver(fn(ClassWithNestedDependencies $dependency) => $dependency);

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
        $resolver = new ClosureResolver(fn($dependency) => $dependency);

        // when/then
        $this->expectException(ParameterNotTypedException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_builtin_type_and_without_default_value(): void {
        // given
        $resolver = new ClosureResolver(function (string $dependency) {
            return new ClassWithBuiltinTypedConstructorDependencyAndWithoutDefaultValue($dependency);
        });

        // when/then
        $this->expectException(ParameterWithBuiltinTypeException::class);
        $resolver->resolve();
    }

    public function test_that_resolves_closure_with_constructor_builtin_dependency_with_default_value(): void {
        // given
        $resolver = new ClosureResolver(function (string $dependency = 'dependency') {
            return new ClassWithBuiltinTypedConstructorDependencyAndWithoutDefaultValue($dependency);
        });

        // when
        $dependency = $resolver->resolve();

        // then
        $this->assertNotEmpty($dependency);
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_union_type(): void {
        // given
        $resolver = new ClosureResolver(fn(string|int $dependency) => $dependency);

        // when/then
        $this->expectException(ParameterWithUnionTypeException::class);
        $resolver->resolve();
    }
}