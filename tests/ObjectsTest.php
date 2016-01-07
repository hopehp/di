<?php


class ObjectsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Hope\Di\IContainer
     */
    protected $c;

    public function setUp()
    {
        $this->c = new \Hope\Di\Container();

        $this->c->define(Hope\DiTest\BazInterface::class, Hope\DiTest\Baz::class);
    }

}