<?php
declare(strict_types=1);

namespace Container\Test\Stub;

class ClassWithConstructorDependency implements ClassDependencyInterface
{
    private ClassWithoutDependency $dependency;

    public function __construct(ClassWithoutDependency $class_without_dependencies)
    {
        $this->dependency = $class_without_dependencies;
    }

    public function getDependency(): ClassWithoutDependency
    {
        return $this->dependency;
    }
}
