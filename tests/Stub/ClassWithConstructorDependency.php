<?php
declare(strict_types=1);

namespace Container\Test\Stub;

class ClassWithConstructorDependency implements ClassDependencyInterface
{
    private ClassWithoutDependency $dependency;

    public function __construct(ClassWithoutDependency $dependency)
    {
        $this->dependency = $dependency;
    }

    public function getDependency(): ClassWithoutDependency
    {
        return $this->dependency;
    }
}
