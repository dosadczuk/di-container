<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace Container\Test\Stub;

use Container\Core\Attributes\Inject;

class ClassWithNonTypedPropertyDependency implements ClassDependencyInterface
{
    #[Inject]
    private $dependency;

    public function getDependency()
    {
        return $this->dependency;
    }
}
