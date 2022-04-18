<?php
declare(strict_types=1);

namespace Container\Test\Unit;

use Container\Dependency;
use Container\Exceptions\DependencyDefinitionRequiredException;
use Container\Exceptions\DependencyNotExistsException;
use Container\Test\Stub\ClassDependencyInterface;
use Container\Test\Stub\ClassWithBuiltinTypedMethodDependency;
use Container\Test\Stub\ClassWithConstructorDependency;
use Container\Test\Stub\ClassWithoutDependency;

it('should create with abstract', function () {
    $dependency = Dependency::transient(ClassWithoutDependency::class);

    expect($dependency->abstract)->toEqual(ClassWithoutDependency::class);
    expect($dependency->definition)->toEqual($dependency->abstract);
});

it('should create with abstract and definition', function () {
    $dependency = Dependency::transient(ClassDependencyInterface::class, ClassWithoutDependency::class);

    expect($dependency->abstract)->toEqual(ClassDependencyInterface::class);
    expect($dependency->definition)->toEqual(ClassWithoutDependency::class);
});

it('should create transient', function () {
    $dependency = Dependency::transient(ClassWithoutDependency::class);

    expect($dependency->is_shared)->toBeFalse();
});

it('should create shared', function () {
    $dependency = Dependency::shared(ClassWithoutDependency::class);

    expect($dependency->is_shared)->toBeTrue();
});

it('should create with no instance', function () {
    $dependency = Dependency::transient(ClassWithoutDependency::class);

    expect($dependency->instance)->toBeNull();
    expect($dependency->isInstantiated())->toBeFalse();
});

it('should create with arguments', function () {
    $arguments = ['dependency' => 'sample'];
    $dependency = Dependency::transient(ClassWithBuiltinTypedMethodDependency::class, null, $arguments);

    expect($dependency->arguments)->toEqual($arguments);
    expect($dependency->hasArgument('dependency'))->toBeTruthy();
    expect($dependency->getArgument('dependency'))->toBe('sample');
    expect($dependency->hasArgument('not_existing'))->toBeFalsy();
    expect($dependency->getArgument('not_existing'))->toBeNull();
});

it('should assign instance', function () {
    $dependency = Dependency::transient(ClassWithoutDependency::class);
    $dependency->instance = new \stdClass();

    expect($dependency->instance)->not->toBeNull();
    expect($dependency->isInstantiated())->toBeTrue();
});

it('should throw exception when creating dependency with not existing class or interface as abstract', function () {
    Dependency::transient('NotExistingClass');
})
    ->throws(DependencyNotExistsException::class);

it('should throw exception when creating dependency with not existing class as definition', function () {
    Dependency::transient(ClassDependencyInterface::class, 'NotExistingClass');
})
    ->throws(DependencyNotExistsException::class);

it('should throw exception when creating dependency with interface as abstract and without definition', function () {
    Dependency::transient(ClassDependencyInterface::class);
})
    ->throws(DependencyDefinitionRequiredException::class);
