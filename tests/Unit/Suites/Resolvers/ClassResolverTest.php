<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Suites\Resolvers;

use Container\Core\Resolvers\ClassResolver;
use Container\Core\Resolvers\Exceptions\ParameterNotTypedException;
use Container\Core\Resolvers\Exceptions\ParameterWithBuiltinTypeException;
use Container\Core\Resolvers\Exceptions\ParameterWithUnionTypeException;
use Container\Core\Resolvers\Exceptions\PropertyNotTypedException;
use Container\Core\Resolvers\Exceptions\PropertyWithBuiltinTypeException;
use Container\Core\Resolvers\Exceptions\PropertyWithUnionTypeException;
use Container\Tests\Unit\Stubs\ClassWithBuiltinTypedConstructorDependencyAndWithDefaultValue;
use Container\Tests\Unit\Stubs\ClassWithBuiltinTypedConstructorDependencyAndWithoutDefaultValue;
use Container\Tests\Unit\Stubs\ClassWithBuiltinTypedPropertyDependency;
use Container\Tests\Unit\Stubs\ClassWithConstructorDependency;
use Container\Tests\Unit\Stubs\ClassWithNestedDependencies;
use Container\Tests\Unit\Stubs\ClassWithNonTypedConstructorDependency;
use Container\Tests\Unit\Stubs\ClassWithNonTypedPropertyDependency;
use Container\Tests\Unit\Stubs\ClassWithoutDependency;
use Container\Tests\Unit\Stubs\ClassWithPropertyDependency;
use Container\Tests\Unit\Stubs\ClassWithSetterDependency;
use Container\Tests\Unit\Stubs\ClassWithUnionTypedConstructorDependency;
use Container\Tests\Unit\Stubs\ClassWithUnionTypedPropertyDependency;
use PHPUnit\Framework\TestCase;

class ClassResolverTest extends TestCase {

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
        $this->expectException(PropertyNotTypedException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_property_with_builtin_type(): void {
        // given
        $resolver = new ClassResolver(ClassWithBuiltinTypedPropertyDependency::class);

        // when/then
        $this->expectException(PropertyWithBuiltinTypeException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_property_with_union_type(): void {
        // given
        $resolver = new ClassResolver(ClassWithUnionTypedPropertyDependency::class);

        // when/then
        $this->expectException(PropertyWithUnionTypeException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_without_type(): void {
        // given
        $resolver = new ClassResolver(ClassWithNonTypedConstructorDependency::class);

        // when/then
        $this->expectException(ParameterNotTypedException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_builtin_type_and_without_default_value(): void {
        // given
        $resolver = new ClassResolver(ClassWithBuiltinTypedConstructorDependencyAndWithoutDefaultValue::class);

        // when/then
        $this->expectException(ParameterWithBuiltinTypeException::class);
        $resolver->resolve();
    }

    public function test_that_resolves_class_with_constructor_builtin_dependency_with_default_value(): void {
        // given
        $resolver = new ClassResolver(ClassWithBuiltinTypedConstructorDependencyAndWithDefaultValue::class);

        // when
        $dependency = $resolver->resolve();

        // then
        $this->assertNotEmpty($dependency);
    }

    public function test_that_throws_exception_trying_to_resolve_parameter_with_union_type(): void {
        // given
        $resolver = new ClassResolver(ClassWithUnionTypedConstructorDependency::class);

        // when/then
        $this->expectException(ParameterWithUnionTypeException::class);
        $resolver->resolve();
    }

    public function test_that_throws_exception_resolving_class_with_cycling_dependencies(): void {
        $this->markTestSkipped('Feature still to add');
    }
}