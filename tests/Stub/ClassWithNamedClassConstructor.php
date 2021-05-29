<?php
declare(strict_types=1);

namespace Foundation\Tests\Stub;

class ClassWithNamedClassConstructor {

    public function __construct(
        public ClassWithoutConstructor $value
    ) {

    }
}