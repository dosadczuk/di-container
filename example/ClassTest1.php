<?php
declare(strict_types=1);

use Foundation\Container\Attribute\Injected;

class ClassTest1 {

    #[Injected]
    private ClassTest2 $class_test_2;
}