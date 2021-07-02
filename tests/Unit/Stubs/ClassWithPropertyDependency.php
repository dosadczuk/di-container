<?php
declare(strict_types=1);

namespace Foundation\Tests\Unit\Stubs;

use Foundation\Container\Attributes\Inject;

class ClassWithPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private ClassWithoutDependency $dependency;

    public function getDependency(): ClassWithoutDependency {
        return $this->dependency;
    }
}