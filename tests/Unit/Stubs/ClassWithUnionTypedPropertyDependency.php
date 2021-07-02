<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Stubs;

use Container\Core\Attributes\Inject;

class ClassWithUnionTypedPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private string|int $dependency = 'dependency';

    public function getDependency(): int|string {
        return $this->dependency;
    }
}