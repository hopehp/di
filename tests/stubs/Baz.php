<?php

namespace Hope\DiTest
{

    use Hope\Di\ContainerInterface;

    class Baz implements BazInterface
    {

        public $container;

        public function __construct(ContainerInterface $container)
        {
            $this->container = $container;
        }

    }

}