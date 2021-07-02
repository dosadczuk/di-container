<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Stubs;

use Container\Core\Attributes\Inject;

class ClassWithPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private ClassWithoutDependency $dependency;

    public function getDependency(): ClassWithoutDependency {
        return $this->dependency;
    }
}