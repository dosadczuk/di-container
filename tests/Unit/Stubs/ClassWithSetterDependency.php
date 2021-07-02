<?php
declare(strict_types=1);

namespace Foundation\Tests\Unit\Stubs;

use Foundation\Container\Attributes\Inject;

class ClassWithSetterDependency implements ClassDependencyInterface {

    private ClassWithoutDependency $dependency;

    #[Inject]
    public function setDependency(ClassWithoutDependency $dependency): void {
        $this->dependency = $dependency;
    }

    public function getDependency(): ClassWithoutDependency {
        return $this->dependency;
    }
}