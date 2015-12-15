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
namespace Hope\Di\Factory
{

    use Hope\Di\IFactory;
    use Hope\Di\IContainer;
    use Hope\Di\IDefinition;

    use Hope\Di\Definition\Object;
    use Hope\Di\Definition\Closure;

    /**
     * Class Base
     *
     * @package Hope\Di\Factory
     */
    class SimpleFactory implements IFactory
    {

        /**
         * @param \Hope\Di\IContainer $container
         * @param string             $name
         * @param mixed              $value
         *
         * @throws \InvalidArgumentException
         *
         * @return IDefinition
         */
        public function define(IContainer $container, $name, $value)
        {
            if (is_object($value)) {
                return new Object($name, get_class($value));
            }
            if (is_string($value) && class_exists($value)) {
                return new Object($name, $value);
            }
            if (is_callable($value)) {
                return new Closure($name, $value);
            }
            throw new \InvalidArgumentException('Undefined value type for container definition');
        }

    }

}