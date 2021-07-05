<?php
declare(strict_types=1);

namespace Container\Test\Unit\Stub;

class ClassWithUnionTypedConstructorDependency implements ClassDependencyInterface {

    private ClassWithoutDependency|ClassWithConstructorDependency $dependency;

    public function __construct(ClassWithoutDependency|ClassWithConstructorDependency $dependency) {
        $this->dependency = $dependency;
    }

    public function getDependency(): ClassWithoutDependency|ClassWithConstructorDependency {
        return $this->dependency;
    }
}