<?php require_once __DIR__ . '/vendor/autoload.php';

$di = new \Hope\Di\Container();
$di->addResolver(new \Hope\Di\Resolver\ReflectionResolver());


$baz = $di->define(Hope\DiTest\BazInterface::class, Hope\DiTest\Baz::class);

/** @var \Hope\DiTest\Bar $bar */
$bar = $di->get(Hope\DiTest\Bar::class);
$baz = $bar->getBaz();

assert($bar instanceof \Hope\DiTest\Bar);
assert($baz instanceof \Hope\DiTest\BazInterface);
assert($baz instanceof \Hope\DiTest\Baz);
