<?php
declare(strict_types=1);

namespace Foundation\Tests\Unit\Stubs;

class ClassWithBuiltinTypedConstructorDependencyAndWithoutDefaultValue implements ClassDependencyInterface {

    private string $dependency;

    public function __construct(string $dependency) {
        $this->dependency = $dependency;
    }

    public function getDependency(): string {
        return $this->dependency;
    }
}