<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ClassTest1.php';
require_once __DIR__ . '/ClassTest2.php';
require_once __DIR__ . '/ClassTest3.php';

use function Foundation\Container\{make, register, register_shared};

register_shared(ClassTest1::class);
register(ClassTest2::class, fn() => new ClassTest2('456'));

$class_1_inst_1 = make(ClassTest1::class);
var_dump($class_1_inst_1);
var_dump(spl_object_id($class_1_inst_1));

$class_1_inst_2 = make(ClassTest1::class);
var_dump($class_1_inst_2);
var_dump(spl_object_id($class_1_inst_2));

$class_1_inst_3 = new ClassTest1();
var_dump($class_1_inst_3);
var_dump(spl_object_id($class_1_inst_3));

$class_2_inst_1 = make(ClassTest2::class);
var_dump($class_2_inst_1);
var_dump(spl_object_id($class_2_inst_1));

$class_2_inst_2 = make(ClassTest2::class);
var_dump($class_2_inst_2);
var_dump(spl_object_id($class_2_inst_2));

$class_2_inst_3 = new ClassTest2();
var_dump($class_2_inst_3);
var_dump(spl_object_id($class_2_inst_3));
