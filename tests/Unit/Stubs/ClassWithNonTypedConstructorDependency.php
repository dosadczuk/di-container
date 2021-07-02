<?php /** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace Container\Tests\Unit\Stubs;

class ClassWithNonTypedConstructorDependency implements ClassDependencyInterface {

    private $dependency;

    public function __construct($dependency) {
        $this->dependency = $dependency;
    }

    public function getDependency() {
        return $this->dependency;
    }
}