<?php
declare(strict_types=1);

namespace Container\Test\Unit\Stub;

use Container\Core\Attribute\Inject;

class ClassWithPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private ClassWithoutDependency $dependency;

    public function getDependency(): ClassWithoutDependency {
        return $this->dependency;
    }
}