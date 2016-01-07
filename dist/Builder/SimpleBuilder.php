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
namespace Hope\Di\Builder
{

    use Hope\Di\IBuilder;
    use Hope\Di\IContainer;
    use Hope\Di\IDefinition;

    use Hope\Di\Definition\Object;
    use Hope\Di\Definition\Closure;

    /**
     * Class Base
     *
     * @package Hope\Di\Builder
     */
    class SimpleBuilder implements IBuilder
    {

        /**
         * Build definition
         *
         * @param \Hope\Di\IContainer   $container
         * @param \Hope\Di\IDefinition $definition
         *
         * @throws \InvalidArgumentException
         *
         * @return mixed
         */
        public function build(IContainer $container, IDefinition $definition)
        {
            if ($definition instanceof Object) {
                return $this->buildObject($container, $definition);
            }
            if ($definition instanceof Closure) {
                return $this->buildFactory($container, $definition);
            }

            throw new \InvalidArgumentException('Can\'t build unknown Definition');
        }

        /**
         * Build Object definitions
         *
         * @param \Hope\Di\IContainer        $container
         * @param \Hope\Di\Definition\Object $definition
         *
         * @protected
         *
         * @return mixed
         */
        protected function buildObject(IContainer $container, Object $definition)
        {
            $classname = $definition->getValue();
            $arguments = $this->resolveValues($container, ...$definition->getArguments());

            // Make instance
            $instance = new $classname(...$arguments);

            // Fill properties
            foreach ($definition->getProperties() as $name => $value) {
                $instance->{$name} = $this->resolveValue($container, $value);
            }

            // Call methods
            foreach ($definition->getMethods() as $name => $value) {
                $instance->{$name}(...$this->resolveValues($container, ...$value));
            }

            return $instance;
        }

        /**
         * Build Closure definition
         *
         * @param \Hope\Di\IContainer         $container
         * @param \Hope\Di\Definition\Closure $definition
         *
         * @protected
         *
         * @return mixed
         */
        protected function buildFactory(IContainer $container, Closure $definition)
        {
            $callable = $definition->getValue();
            $arguments = $definition->getArguments();

            return $callable(...$this->resolveValues($container, ...$arguments));

        }

        protected function resolveValue(IContainer $container, $name)
        {
            return $container->get($name);
        }

        /**
         * Resolve dependencies
         *
         * @param \Hope\Di\IContainer $container
         * @param  mixed              ...$names
         *
         * @return array
         */
        protected function resolveValues(IContainer $container, ...$names)
        {
            return array_map(function($name) use ($container) {
                return $this->resolveValue($container, $name);
            }, $names);
        }

    }

}