<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Container\Test\Unit\Resolvers;

use Container\Exceptions\ContainerException;
use Container\Resolvers\ClosureResolver;
use Container\Test\Stub\ClassWithConstructorDependency;
use Container\Test\Stub\ClassWithNestedDependencies;
use Container\Test\Stub\ClassWithoutDependency;
use Container\Test\Stub\ClassWithPropertyDependency;
use Container\Test\Stub\ClassWithSetterDependency;

it('should resolve closure without parameters', function () {
    $resolver = new ClosureResolver(fn() => new ClassWithoutDependency());

    $instance = $resolver->resolve();

    expect($instance)->toBeInstanceOf(ClassWithoutDependency::class);
});

it('should resolve closure with parameter of class without dependencies', function () {
    $resolver = new ClosureResolver(fn(ClassWithConstructorDependency $dependency) => $dependency);

    /** @var ClassWithConstructorDependency $instance */
    $instance = $resolver->resolve();

    expect($instance)->toBeInstanceOf(ClassWithConstructorDependency::class);
    expect($instance->getDependency())->not->toBeNull();
});

it('should resolve closure with parameter of class with property dependency', function () {
    $resolver = new ClosureResolver(fn(ClassWithPropertyDependency $dependency) => $dependency);

    /** @var ClassWithPropertyDependency $instance */
    $instance = $resolver->resolve();

    expect($instance)->toBeInstanceOf(ClassWithPropertyDependency::class);
    expect($instance->getDependency())->not->toBeNull();
});

it('should resolve closure with parameter of class with setter dependency', function () {
    $resolver = new ClosureResolver(fn(ClassWithSetterDependency $dependency) => $dependency);

    /** @var ClassWithSetterDependency $instance */
    $instance = $resolver->resolve();

    expect($instance)->toBeInstanceOf(ClassWithSetterDependency::class);
    expect($instance->getDependency())->not->toBeNull();
});

it('should resolve closure with parameter of class with nested dependencies', function () {
    $resolver = new ClosureResolver(fn(ClassWithNestedDependencies $dependency) => $dependency);

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

it('should throw exception when resolving parameter without type', function () {
    $resolver = new ClosureResolver(fn($dependency) => $dependency);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving parameter with builtin type', function () {
    $resolver = new ClosureResolver(fn(string $dependency) => $dependency);
    $resolver->resolve();
})
    ->throws(ContainerException::class);

it('should throw exception when resolving parameter with union type', function () {
    $resolver = new ClosureResolver(fn(string|int $dependency) => $dependency);
    $resolver->resolve();
})
    ->throws(ContainerException::class);
