<?php
/** @noinspection PhpClosureCanBeConvertedToFirstClassCallableInspection */
declare(strict_types=1);

namespace Container\Test\Unit\Resolvers;

use Container\Exceptions\ContainerException;
use Container\Resolvers\ClassResolver;
use Container\Resolvers\ClosureResolver;
use Container\Resolvers\ResolverFactory;
use Container\Test\Stub\ClassWithoutDependency;

beforeEach(function () {
    $this->factory = new ResolverFactory();
});

it('should create ClassResolver', function () {
    $resolver = $this->factory->createResolver(ClassWithoutDependency::class);

    expect($resolver)->toBeInstanceOf(ClassResolver::class);
});

it('should create ClosureResolver (from function)', function () {
    $resolver = $this->factory->createResolver(fn() => true);

    expect($resolver)->toBeInstanceOf(ClosureResolver::class);
});

it('should create ClosureResolver (from Closure)', function () {
    $resolver = $this->factory->createResolver(\Closure::fromCallable(fn() => true));

    expect($resolver)->toBeInstanceOf(ClosureResolver::class);
});

it('should throw exception when creating resolver for not resolvable thing', function () {
    $this->factory->createResolver('thing');
})
    ->throws(ContainerException::class);
