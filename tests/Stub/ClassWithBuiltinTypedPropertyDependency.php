<?php
declare(strict_types=1);

namespace Container\Test\Stub;

use Container\Core\Attributes\Inject;

class ClassWithBuiltinTypedPropertyDependency implements ClassDependencyInterface
{
    #[Inject]
    private string $dependency = 'dependency';

    public function getDependency(): string
    {
        return $this->dependency;
    }
}
