<?php /** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace Container\Tests\Unit\Stubs;

use Container\Core\Attributes\Inject;

class ClassWithNonTypedPropertyDependency implements ClassDependencyInterface {

    #[Inject]
    private $dependency;

    public function getDependency() {
        return $this->dependency;
    }
}