<?php
declare(strict_types=1);

namespace Foundation\Tests\Unit\Stubs;

use Foundation\Container\Attributes\Inject;

class ClassWithBuiltinTypedPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private string $dependency = 'dependency';

    public function getDependency(): string {
        return $this->dependency;
    }
}