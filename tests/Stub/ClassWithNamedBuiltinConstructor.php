<?php
declare(strict_types=1);

namespace Foundation\Tests\Stub;

class ClassWithNamedBuiltinConstructor {

    public function __construct(
        public string $value = '123'
    ) {
    }
}