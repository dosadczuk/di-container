<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ClassTest1.php';
require_once __DIR__ . '/ClassTest2.php';

use function Foundation\Container\{make, register};

register(ClassTest1::class, fn(ClassTest2 $test_2) => new ClassTest1($test_2));
register(ClassTest2::class, fn() => new ClassTest2('456'));

$dependency = make(ClassTest2::class);

var_dump($dependency);