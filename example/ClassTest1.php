<?php
declare(strict_types=1);

use Foundation\Container\Attribute\Inject;

class ClassTest1 {

    private ClassTest2 $class_test_2;

    #[Inject]
    private ClassTest3 $class_test_3;

    #[Inject]
    public function setClassTest2(ClassTest2 $class_test_2): void {
        $this->class_test_2 = $class_test_2;
    }

    public function getClassTest2(): ClassTest2 {
        return $this->class_test_2;
    }
}