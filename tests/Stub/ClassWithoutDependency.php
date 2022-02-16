<?php
declare(strict_types=1);

namespace Container\Test\Stub;

class ClassWithoutDependency implements ClassDependencyInterface
{
    private string $value;

    public function __construct()
    {
        $this->value = uniqid('', true);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
