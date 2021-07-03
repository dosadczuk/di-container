<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Stub;

use Container\Core\Attribute\Inject;

class ClassWithBuiltinTypedPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private string $dependency = 'dependency';

    public function getDependency(): string {
        return $this->dependency;
    }
}