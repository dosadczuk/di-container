<?php
declare(strict_types=1);

namespace Container\Test\Unit\Stub;

class ClassOneWithClassTwoConstructorDependency implements ClassDependencyInterface {

    private ClassTwoWithClassOneConstructorDependency $dependency_with_cycle;

    private ClassWithPropertyDependency $dependency_with_property;

    public function __construct(
        ClassTwoWithClassOneConstructorDependency $dependency_with_cycle,
        ClassWithPropertyDependency               $dependency_with_property
    ) {
        $this->dependency_with_cycle = $dependency_with_cycle;
        $this->dependency_with_property = $dependency_with_property;
    }

    public function getDependencyWithCycle(): ClassTwoWithClassOneConstructorDependency {
        return $this->dependency_with_cycle;
    }

    public function getDependencyWithProperty(): ClassWithPropertyDependency {
        return $this->dependency_with_property;
    }
}