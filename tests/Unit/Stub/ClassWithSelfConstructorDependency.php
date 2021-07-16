<?php
declare(strict_types=1);

namespace Container\Test\Unit\Stub;

class ClassWithSelfConstructorDependency implements ClassDependencyInterface {

    private ClassWithSelfConstructorDependency $dependency;

    public function __construct(ClassWithSelfConstructorDependency $dependency) {
        $this->dependency = $dependency;
    }

    public function getDependency(): ClassWithSelfConstructorDependency {
        return $this->dependency;
    }
}