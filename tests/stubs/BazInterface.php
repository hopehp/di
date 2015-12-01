<?php

namespace Hope\DiTest
{

    use Hope\Di\ContainerInterface;

    interface BazInterface
    {

        public function __construct(ContainerInterface $container);

    }

}