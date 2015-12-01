<?php

namespace Hope\DiTest
{

    class Bar
    {

        protected $baz;

        public function __construct(BazInterface $baz)
        {
            $this->baz = $baz;
        }

    }

}