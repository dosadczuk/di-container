<?php
declare(strict_types=1);

use Container\Core\ContainerException;
use Container\Core\Dependency;
use Container\Core\DependencyNotFoundException;
use Container\Core\DependencyRegistry;
use Container\Test\Stub\ClassDependencyInterface;
use Container\Test\Stub\ClassWithoutDependency;
use Container\Test\Stub\ClassWithPropertyDependency;
use Container\Test\Stub\ClassWithSetterDependency;

beforeEach(function () {
    $this->registry = new DependencyRegistry();
});

test('created registry is empty', function () {
    expect($this->registry)->toBeEmpty();
});

it('should add dependency', function () {
    $dependency = Dependency::transient(ClassWithoutDependency::class);

    $this->registry->add($dependency);

    expect($this->registry)->toHaveCount(1);
    expect($this->registry->has($dependency->abstract))->toBeTrue();
});

it('should throw exception when dependency is already added', function () {
    $dependency = Dependency::transient(ClassWithoutDependency::class);

    $this->registry->add($dependency);
    $this->registry->add($dependency);
})
    ->throws(ContainerException::class);

it('should throw exception when dependency is not added', function () {
    expect($this->registry->get(ClassWithoutDependency::class));
})
    ->throws(DependencyNotFoundException::class);

it('should get transient dependency', function () {
    $dependency = Dependency::transient(ClassWithoutDependency::class);

    $this->registry->add($dependency);

    $instance1 = $this->registry->get(ClassWithoutDependency::class);
    $instance2 = $this->registry->get(ClassWithoutDependency::class);

    expect($instance1)->toBeInstanceOf(ClassWithoutDependency::class);
    expect($instance1)->toBeInstanceOf(ClassWithoutDependency::class);
    expect($instance1)->not->toBe($instance2);
});

it('should get shared dependency', function () {
    $dependency = Dependency::shared(ClassWithoutDependency::class);

    $this->registry->add($dependency);

    $instance1 = $this->registry->get(ClassWithoutDependency::class);
    $instance2 = $this->registry->get(ClassWithoutDependency::class);

    expect($instance1)->toBeInstanceOf(ClassWithoutDependency::class);
    expect($instance2)->toBeInstanceOf(ClassWithoutDependency::class);
    expect($instance1)->toBe($instance2);
});

it('should make dependency', function () {
    $instance = $this->registry->make(ClassWithoutDependency::class);

    expect($instance)->toBeInstanceOf(ClassWithoutDependency::class);
});

it('should check if has dependency', function () {
    $dependency1 = Dependency::transient(ClassDependencyInterface::class, ClassWithoutDependency::class);
    $dependency2 = Dependency::transient(ClassWithoutDependency::class);

    $this->registry->add($dependency1);

    expect($this->registry->has($dependency1->abstract))->toBeTrue();
    expect($this->registry->has($dependency2->abstract))->toBeFalse();
});

it('should remove dependency', function () {
    $dependency = Dependency::transient(ClassWithoutDependency::class);

    $this->registry->add($dependency);
    $this->assertTrue($this->registry->has($dependency->abstract));
    $this->registry->remove($dependency->abstract);

    expect($this->registry->has($dependency->abstract))->toBeFalse();
});

it('should throw exception when removing not added dependency', function () {
    $dependency = Dependency::transient(ClassWithoutDependency::class);

    $this->assertFalse($this->registry->has($dependency->abstract));
    $this->registry->remove($dependency->abstract);
})
    ->throws(ContainerException::class);

it('should merge dependencies', function () {
    $dependencies = [
        Dependency::transient(ClassWithoutDependency::class),
        Dependency::transient(ClassWithPropertyDependency::class),
        Dependency::transient(ClassWithSetterDependency::class),
    ];

    $this->assertEmpty($this->registry);
    $this->registry->merge($dependencies);

    expect($this->registry)->toHaveCount(3);
});
