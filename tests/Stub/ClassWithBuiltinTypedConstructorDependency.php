<?php
declare(strict_types=1);

namespace Container\Test\Stub;

class ClassWithBuiltinTypedConstructorDependency implements ClassDependencyInterface
{
    private string $dependency;

    public function __construct(string $dependency = 'dependency')
    {
        $this->dependency = $dependency;
    }

    public function getDependency(): string
    {
        return $this->dependency;
    }
}
