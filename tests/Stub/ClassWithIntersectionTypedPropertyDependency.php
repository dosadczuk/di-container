<?php
declare(strict_types=1);

namespace Container\Test\Stub;

use Container\Attributes\Inject;

class ClassWithIntersectionTypedPropertyDependency implements ClassDependencyInterface
{
    #[Inject]
    private ClassWithPropertyDependency&ClassWithSetterDependency $dependency;

    public function getDependency(): ClassWithSetterDependency&ClassWithPropertyDependency
    {
        return $this->dependency;
    }
}
