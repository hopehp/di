<?php require_once __DIR__ . '/vendor/autoload.php';
$timeStart = microtime(true);

$di = new \Hope\Di\Container();
$di->setResolver(new \Hope\Di\Resolver\ReflectionResolver());

$di->add(\Hope\DiTest\Foo::class);

$di->add(\Hope\DiTest\Bar::class)
    ->argument('baz', \Hope\DiTest\BazInterface::class)
;

$di->add(\Hope\DiTest\Baz::class)
    ->argument('container', \Hope\Di\IContainer::class)
;

$di->add(\Hope\DiTest\BazInterface::class, \Hope\DiTest\Baz::class);


$di->get(\Hope\DiTest\Bar::class);
$di->get(\Hope\DiTest\Foo::class);
var_dump($di->get(\Hope\DiTest\Bar::class));

$timeTotal = (microtime(true) - $timeStart);
echo $timeTotal;
