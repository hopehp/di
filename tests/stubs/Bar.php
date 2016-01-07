<?php

namespace Hope\DiTest
{

    class Bar
    {

        protected $_baz;

        public function __construct(BazInterface $baz)
        {
            $this->_baz = $baz;
        }

        public function getBaz()
        {
            return $this->_baz;
        }

    }

}