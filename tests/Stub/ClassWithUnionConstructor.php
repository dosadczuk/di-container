<?php
declare(strict_types=1);

namespace Foundation\Tests\Stub;

class ClassWithUnionConstructor {

    public function __construct(
        public int|string $value
    ) {
    }
}