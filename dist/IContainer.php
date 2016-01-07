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

    /**
     * Interface IContainer
     *
     * @package Hope\Di
     */
    interface IContainer
    {

        /**
         * Register value
         *
         * @param $name
         * @param $value
         *
         * @return \Hope\Di\IContainer
         */
        public function set($name, $value);

        /**
         * Returns registered value
         *
         * @param string $name
         * @param bool   $throw [optional]
         *
         * @throws \InvalidArgumentException
         *
         * @return mixed
         */
        public function get($name, $throw = true);

        /**
         * Returns true if service is registered
         *
         * @param string $name
         *
         * @return bool
         */
        public function has($name);


        /**
         * Build definition
         *
         * @param \Hope\Di\IDefinition $definition
         *
         * @return mixed
         */
        public function build(IDefinition $definition);

        /**
         * Define service
         *
         * @param string $name
         * @param mixed  $value [optional]
         *
         * @return IDefinition
         */
        public function define($name, $value = null);

        /**
         * Create a new container which related to this
         *
         * @return \Hope\Di\IContainer
         */
        public function isolate();

        /**
         * Returns `true` if the container is isolated
         *
         * @return bool
         */
        public function isolated();

        /**
         * Register values in provider
         *
         * @param \Hope\Di\IProvider $provider
         *
         * @return \Hope\Di\IContainer
         */
        public function register(IProvider $provider);

        /**
         * Returns definitions factory
         *
         * @return \Hope\Di\IFactory
         */
        public function getFactory();


    }

}