<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ClassTest1.php';
require_once __DIR__ . '/ClassTest2.php';

use Foundation\Container\Container;

$container = Container::getInstance();
$container->register(ClassTest1::class, fn(ClassTest2 $test_2) => new ClassTest1($test_2));
$container->register(ClassTest2::class, fn() => new ClassTest2('456'));

var_dump($container->make(ClassTest2::class));