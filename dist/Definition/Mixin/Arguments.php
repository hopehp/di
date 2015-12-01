<?php
/**
 * Hope - PHP 5.6 framework
 *
 * @author      Shvorak Alexey <dr.emerido@gmail.com>
 * @copyright   2011 Shvorak Alexey
 * @version     0.1.0
 * @package     Hope
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Hope\Di\Definition\Mixin
{

    trait Arguments
    {

        /**
         * @var array
         */
        protected $_arguments = [];

        /**
         * Register argument by name or index
         *
         * @param string $name
         * @param mixed  $value
         *
         * @return $this
         */
        public function setArgument($name, $value)
        {
            $this->_arguments[$name] = $value;
            return $this;
        }

        /**
         * Register arguments
         *
         * @param array $arguments
         *
         * @return $this
         */
        public function setArguments(array $arguments)
        {
            foreach ($arguments as $name => $value) {
                $this->_arguments[$name] = $value;
            }
            return $this;
        }

        /**
         * Returns registered arguments
         *
         * @return array
         */
        public function getArguments()
        {
            return $this->_arguments;
        }

        /**
         * Alias for setArgument()
         *
         * @param string $name
         * @param mixed  $value
         *
         * @return $this
         */
        public function argument($name, $value)
        {
            return $this->setArgument($name, $value);
        }

        /**
         * Alias for setArguments()
         *
         * @param array $arguments
         *
         * @return $this
         */
        public function arguments(array $arguments)
        {
            return $this->setArguments($arguments);
        }

    }

}