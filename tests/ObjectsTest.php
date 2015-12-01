<?php


class ObjectsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Hope\Di\ContainerInterface
     */
    protected $c;

    public function setUp()
    {
        $this->c = new \Hope\Di\Container();
        $this->c->setBuilder(new \Hope\Di\Builder\SimpleBuilder());
        $this->c->setResolver(new \Hope\Di\Resolver\SimpleResolver());

        $this->c->object(Hope\DiTest\BazInterface::class, Hope\DiTest\Baz::class);

        $this->c->set('baz', Hope\DiTest\BazInterface::class);
        $this->c->set(Hope\DiTest\BazInterface::class, 'baz');

    }

}