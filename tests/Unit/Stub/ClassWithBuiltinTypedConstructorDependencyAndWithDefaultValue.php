<?php
declare(strict_types=1);

namespace Container\Tests\Unit\Stub;

class ClassWithBuiltinTypedConstructorDependencyAndWithDefaultValue implements ClassDependencyInterface {

    private string $dependency;

    public function __construct(string $dependency = 'dependency') {
        $this->dependency = $dependency;
    }

    public function getDependency(): string {
        return $this->dependency;
    }
}