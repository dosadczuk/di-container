<?php
declare(strict_types=1);

namespace Container\Test\Stub;

use Container\Attributes\Inject;

class ClassWithUnionTypedPropertyDependency implements ClassDependencyInterface
{
    #[Inject]
    private string|int $dependency = 'dependency';

    public function getDependency(): int|string
    {
        return $this->dependency;
    }
}
