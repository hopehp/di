<?php

namespace Hope\DiTest
{

    use Hope\Di\IContainer;

    interface BazInterface
    {

        public function __construct(IContainer $container);

    }

}