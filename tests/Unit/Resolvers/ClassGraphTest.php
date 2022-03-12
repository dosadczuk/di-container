<?php
declare(strict_types=1);

namespace Container\Test\Unit\Resolvers;

use Container\Resolvers\ClassGraph;
use Container\Test\Stub\ClassOneWithClassTwoConstructorDependency;
use Container\Test\Stub\ClassWithoutDependency;
use Container\Test\Stub\ClassWithPropertyDependency;
use Container\Test\Stub\ClassWithSelfConstructorDependency;

beforeEach(function () {
    $graph = new \ReflectionClass(ClassGraph::class);
    // clear "cache"
    $property = $graph->getProperty('class_adjacency_lists');
    $property->setValue([]);
});

test('class without dependencies should not be cyclic', function () {
    $graph = new ClassGraph(ClassWithoutDependency::class);

    expect($graph->isCyclic())->toBeFalse();
});

test('class without cyclic dependencies should not be cyclic', function () {
    $graph = new ClassGraph(ClassWithPropertyDependency::class);

    expect($graph->isCyclic())->toBeFalse();
});

test('class with cyclic dependencies should be cyclic', function () {
    $graph = new ClassGraph(ClassOneWithClassTwoConstructorDependency::class);

    expect($graph->isCyclic())->toBeTrue();
});

test('class with self as dependency should be cyclic', function () {
    $graph = new ClassGraph(ClassWithSelfConstructorDependency::class);

    expect($graph->isCyclic())->toBeTrue();
});
