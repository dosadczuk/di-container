<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Suite\Dependency\Resolver;

use Container\Core\Dependency\Resolver\ClassDependencyResolver;
use Container\Core\Dependency\Resolver\ClosureDependencyResolver;
use Container\Core\Dependency\Resolver\DependencyResolverFactory;
use Container\Core\Dependency\Resolver\Exception\DependencyResolverNotFoundException;
use Container\Tests\Unit\Stub\ClassWithoutDependency;
use PHPUnit\Framework\TestCase;

class DependencyResolverFactoryTest extends TestCase {

    private DependencyResolverFactory $factory;

    protected function setUp(): void {
        $this->factory = new DependencyResolverFactory();
    }

    public function test_that_creates_class_resolver_for_class_definition(): void {
        // when
        $resolver = $this->factory->createResolver(ClassWithoutDependency::class);

        // then
        $this->assertInstanceOf(ClassDependencyResolver::class, $resolver);
    }

    public function test_that_creates_closure_resolver_for_closure_definition(): void {
        // when
        $resolver = $this->factory->createResolver(fn() => new ClassWithoutDependency());

        // then
        $this->assertInstanceOf(ClosureDependencyResolver::class, $resolver);
    }

    public function test_that_throws_exception_for_not_supported_definition(): void {
        // when/then
        $this->expectException(DependencyResolverNotFoundException::class);
        $this->factory->createResolver(false);
    }
}