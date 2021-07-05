<?php
declare(strict_types=1);

namespace Container\Test\Unit\Stub;

use Container\Core\Attribute\Inject;

class ClassWithUnionTypedPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private string|int $dependency = 'dependency';

    public function getDependency(): int|string {
        return $this->dependency;
    }
}