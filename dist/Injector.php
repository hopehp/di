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
namespace Hope\Di
{

    use Hope\Di\Definition\Closure;
    use Hope\Di\Definition\Object;

    /**
     * Class Injector
     *
     * @package Hope\Di
     */
    class Injector implements IInjector
    {

        /**
         * @var \Hope\Di\IContainer
         */
        protected $_container;

        /**
         * @inheritdoc
         */
        public function setContainer(IContainer $container)
        {
            $this->_container = $container;
            return $this;
        }

        /**
         * @inheritdoc
         */
        public function getContainer()
        {
            return $this->_container;
        }

        /**
         * @inheritdoc
         */
        public function call(callable $function, array $locals = [])
        {
            return $this->getContainer()->build(new Closure('anonymous', $function));
        }

        /**
         * @inheritdoc
         */
        public function make($class, array $locals = [])
        {
            return $this->getContainer()->build(new Object(get_class($class), $class));
        }

    }

}