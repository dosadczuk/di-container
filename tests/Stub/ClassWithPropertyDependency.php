<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
declare(strict_types=1);

namespace Container\Test\Stub;

use Container\Attributes\Inject;

class ClassWithPropertyDependency implements ClassDependencyInterface
{
    #[Inject]
    private ClassWithoutDependency $dependency;

    public function getDependency(): ClassWithoutDependency
    {
        return $this->dependency;
    }
}
