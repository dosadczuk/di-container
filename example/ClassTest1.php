<?php
declare(strict_types=1);

class ClassTest1 {

    public function __construct(
        public ClassTest2 $class_test_2
    ) {
    }
}