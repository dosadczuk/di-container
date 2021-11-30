<?php
declare(strict_types=1);

namespace Container\Test\Unit\Stub;

class ClassTwoWithClassOneConstructorDependency implements ClassDependencyInterface {

    private ClassOneWithClassTwoConstructorDependency $dependency_with_cycle;

    private ClassWithSetterDependency $dependency_with_setter;

    public function __construct(
        ClassOneWithClassTwoConstructorDependency $dependency_with_cycle,
        ClassWithSetterDependency                 $dependency_with_setter
    ) {
        $this->dependency_with_cycle = $dependency_with_cycle;
        $this->dependency_with_setter = $dependency_with_setter;
    }

    public function getDependencyWithCycle(): ClassOneWithClassTwoConstructorDependency {
        return $this->dependency_with_cycle;
    }

    public function getDependencyWithSetter(): ClassWithSetterDependency {
        return $this->dependency_with_setter;
    }
}