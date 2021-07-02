<?php
declare(strict_types=1);

namespace Foundation\Tests\Unit\Stubs;

class ClassWithoutDependency implements ClassDependencyInterface {

    private string $value;

    public function __construct() {
        $this->value = uniqid();
    }

    public function getValue(): string {
        return $this->value;
    }
}