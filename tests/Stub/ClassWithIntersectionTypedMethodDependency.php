<?php
declare(strict_types=1);

namespace Container\Test\Stub;

class ClassWithIntersectionTypedMethodDependency implements ClassDependencyInterface
{
    private ClassWithPropertyDependency&ClassWithSetterDependency $dependency;

    public function __construct(ClassWithPropertyDependency&ClassWithSetterDependency $dependency)
    {
        $this->dependency = $dependency;
    }

    public function getDependency(): ClassWithPropertyDependency&ClassWithSetterDependency
    {
        return $this->dependency;
    }
}
