<?php /** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace Foundation\Tests\Unit\Stubs;

use Foundation\Container\Attributes\Inject;

class ClassWithNonTypedPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private $dependency;

    public function getDependency() {
        return $this->dependency;
    }
}