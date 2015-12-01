<?php require_once __DIR__ . '/vendor/autoload.php';


$di = new \Hope\Di\Container();
$di->setResolver(new \Hope\Di\Resolver\ReflectionResolver());


$di->add(\Hope\DiTest\Bar::class);

$di->add(\Hope\DiTest\Baz::class);
$di->add(\Hope\DiTest\BazInterface::class, \Hope\DiTest\Baz::class);


var_dump($di->get(\Hope\DiTest\Bar::class));