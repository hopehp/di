<?php

namespace Hope\DiTest
{

    use Hope\Di\IContainer;

    class Baz implements BazInterface
    {

        public $_foo;

        public $_test;

        public function __construct(IContainer $container)
        {
            $this->_foo = $container->get(Foo::class);
        }

    }

}