<?php
declare(strict_types=1);

namespace Container\Test\Unit\Suite\Resolver;

use Container\Core\Resolver\ClassResolver;
use Container\Core\Resolver\ClosureResolver;
use Container\Core\Resolver\ResolverException;
use Container\Core\Resolver\ResolverFactory;
use Container\Test\Unit\Stub\ClassWithoutDependency;
use PHPUnit\Framework\TestCase;

class DependencyResolverFactoryTest extends TestCase {

    private ResolverFactory $factory;

    protected function setUp(): void {
        $this->factory = new ResolverFactory();
    }

    public function test_that_creates_class_resolver_for_class_definition(): void {
        // when
        $resolver = $this->factory->createResolver(ClassWithoutDependency::class);

        // then
        $this->assertInstanceOf(ClassResolver::class, $resolver);
    }

    public function test_that_creates_closure_resolver_for_closure_definition(): void {
        // when
        $resolver = $this->factory->createResolver(fn() => new ClassWithoutDependency());

        // then
        $this->assertInstanceOf(ClosureResolver::class, $resolver);
    }

    public function test_that_throws_exception_for_not_supported_definition(): void {
        // when/then
        $this->expectException(ResolverException::class);
        $this->factory->createResolver(false);
    }
}