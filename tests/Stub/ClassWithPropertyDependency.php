<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);

namespace Container\Test\Stub;

use Container\Core\Attributes\Inject;

class ClassWithPropertyDependency implements ClassDependencyInterface
{
    #[Inject]
    private ClassWithoutDependency $dependency;

    public function getDependency(): ClassWithoutDependency
    {
        return $this->dependency;
    }
}
