<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Container\Test\Unit\Resolvers;

use Container\Exceptions\ContainerException;
use Container\Resolvers\ClassResolver;
use Container\Test\Stub\ClassOneWithClassTwoConstructorDependency;
use Container\Test\Stub\ClassWithBuiltinTypedMethodDependency;
use Container\Test\Stub\ClassWithBuiltinTypedPropertyDependency;
use Container\Test\Stub\ClassWithConstructorDependency;
use Container\Test\Stub\ClassWithIntersectionTypedMethodDependency;
use Container\Test\Stub\ClassWithIntersectionTypedPropertyDependency;
use Container\Test\Stub\ClassWithNestedDependencies;
use Container\Test\Stub\ClassWithNonTypedMethodDependency;
use Container\Test\Stub\ClassWithNonTypedPropertyDependency;
use Container\Test\Stub\ClassWithoutDependency;
use Container\Test\Stub\ClassWithPropertyDependency;
use Container\Test\Stub\ClassWithSelfConstructorDependency;
use Container\Test\Stub\ClassWithSetterDependency;
use Container\Test\Stub\ClassWithUnionTypedConstructorDependency;
use Container\Test\Stub\ClassWithUnionTypedPropertyDependency;

it('should resolve class without dependencies', function () {
    $resolver = new ClassResolver(ClassWithoutDependency::class);

    $instance = $resolver->resolve();

    expect($instance)->toBeInstanceOf(ClassWithoutDependency::class);
});

it('should resolve class with constructor dependency', function () {
    $resolver = new ClassResolver(ClassWithConstructorDependency::class);

    /** @var ClassWithConstructorDependency $instance */
    $instance = $resolver->resolve();

    expect($instance)->toBeInstanceOf(ClassWithConstructorDependency::class);
    expect($instance->getDependency())->not->toBeNull();
});

it('should resolve class with property dependency', function () {
    $resolver = new ClassResolver(ClassWithPropertyDependency::class);

    /** @var ClassWithPropertyDependency $instance */
    $instance = $resolver->resolve();

    expect($instance)->toBeInstanceOf(ClassWithPropertyDependency::class);
    expect($instance->getDependency())->not->toBeNull();
});

it('should resolve class with setter dependency', function () {
    $resolver = new ClassResolver(ClassWithSetterDependency::class);

    /** @var ClassWithSetterDependency $instance */
    $instance = $resolver->resolve();

    expect($instance)->toBeInstanceOf(ClassWithSetterDependency::class);
    expect($instance->getDependency())->not->toBeNull();
});

it('should resolve class with nested dependencies', function () {
    $resolver = new ClassResolver(ClassWithNestedDependencies::class);

    /** @var ClassWithNestedDependencies $instance */
    $instance = $resolver->resolve();

    expect($instance)->toBeInstanceOf(ClassWithNestedDependencies::class);
    expect($instance->getDependencyWithConstructor())->not->toBeNull();
    expect($instance->getDependencyWithConstructor()->getDependency())->not->toBeNull();
    expect($instance->getDependencyWithProperty())->not->toBeNull();
    expect($instance->getDependencyWithProperty()->getDependency())->not->toBeNull();
    expect($instance->getDependencyWithSetter())->not->toBeNull();
    expect($instance->getDependencyWithSetter()->getDependency())->not->toBeNull();
});

it('should throw exception when resolving property without type', function () {
    $resolver = new ClassResolver(ClassWithNonTypedPropertyDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving property with intersection type', function () {
    $resolver = new ClassResolver(ClassWithIntersectionTypedPropertyDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving property with builtin type and no argument for it', function () {
    $resolver = new ClassResolver(ClassWithBuiltinTypedPropertyDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving property with union type and no argument for it', function () {
    $resolver = new ClassResolver(ClassWithUnionTypedPropertyDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving parameter without type', function () {
    $resolver = new ClassResolver(ClassWithNonTypedMethodDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving parameter with intersection type', function () {
    $resolver = new ClassResolver(ClassWithIntersectionTypedMethodDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving parameter with builtin type and no argument for it', function () {
    $resolver = new ClassResolver(ClassWithBuiltinTypedMethodDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving parameter with union type and no argument for it', function () {
    $resolver = new ClassResolver(ClassWithUnionTypedConstructorDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving class with self as constructor dependency', function () {
    $resolver = new ClassResolver(ClassWithSelfConstructorDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving class with cyclic dependency', function () {
    $resolver = new ClassResolver(ClassOneWithClassTwoConstructorDependency::class);
    $resolver->resolve();
})
    ->throws(ContainerException::class);
