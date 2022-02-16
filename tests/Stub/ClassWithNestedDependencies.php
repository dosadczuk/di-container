<?php
declare(strict_types=1);

namespace Container\Test\Stub;

class ClassWithNestedDependencies implements ClassDependencyInterface
{
    private ClassWithConstructorDependency $dependency_with_constructor;

    private ClassWithPropertyDependency $dependency_with_property;

    private ClassWithSetterDependency $dependency_with_setter;

    public function __construct(
        ClassWithConstructorDependency $dependency_with_constructor,
        ClassWithPropertyDependency $dependency_with_property,
        ClassWithSetterDependency $dependency_with_setter
    ) {
        $this->dependency_with_constructor = $dependency_with_constructor;
        $this->dependency_with_property = $dependency_with_property;
        $this->dependency_with_setter = $dependency_with_setter;
    }

    public function getDependencyWithConstructor(): ClassWithConstructorDependency
    {
        return $this->dependency_with_constructor;
    }

    public function getDependencyWithProperty(): ClassWithPropertyDependency
    {
        return $this->dependency_with_property;
    }

    public function getDependencyWithSetter(): ClassWithSetterDependency
    {
        return $this->dependency_with_setter;
    }
}
