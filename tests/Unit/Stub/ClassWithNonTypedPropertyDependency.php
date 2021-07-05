<?php /** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace Container\Test\Unit\Stub;

use Container\Core\Attribute\Inject;

class ClassWithNonTypedPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private $dependency;

    public function getDependency() {
        return $this->dependency;
    }
}