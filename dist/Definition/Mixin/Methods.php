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

    /**
     * Class Methods
     *
     * @package Hope\Di\Definition\Mixin
     */
    trait Methods
    {

        /**
         * @var array
         */
        protected $_methods = [];

        /**
         * Register method setter
         *
         * @param string $name
         * @param mixed  $value
         *
         * @return $this
         */
        public function setMethod($name, $value)
        {
            $this->_methods[$name] = $value;

            return $this;
        }

        /**
         * Register methods
         *
         * @param array $methods
         *
         * @return $this
         */
        public function setMethods(array $methods)
        {
            foreach ($methods as $name => $value) {
                $this->_methods[$name] = $value;
            }
            return $this;
        }

        /**
         * Returns registered methods
         *
         * @return array
         */
        public function getMethods()
        {
            return $this->_methods;
        }

        /**
         * Alias for setMethod()
         *
         * @param string $name
         * @param mixed  $value
         *
         * @return $this
         */
        public function method($name, $value)
        {
            return $this->setMethod($name, $value);
        }

        /**
         * Alias for setMethods()
         *
         * @param array $methods
         *
         * @return $this
         */
        public function methods(array $methods)
        {
            return $this->setMethods($methods);
        }

    }

}