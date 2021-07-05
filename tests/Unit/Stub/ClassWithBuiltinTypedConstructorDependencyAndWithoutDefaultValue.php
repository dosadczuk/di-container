<?php
declare(strict_types=1);

namespace Container\Test\Unit\Stub;

class ClassWithBuiltinTypedConstructorDependencyAndWithoutDefaultValue implements ClassDependencyInterface {

    private string $dependency;

    public function __construct(string $dependency) {
        $this->dependency = $dependency;
    }

    public function getDependency(): string {
        return $this->dependency;
    }
}