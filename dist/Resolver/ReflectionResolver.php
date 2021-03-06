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
namespace Hope\Di\Resolver
{

    use Hope\Di\IResolver;
    use Hope\Di\IContainer;
    use Hope\Di\IDefinition;
    use Hope\Di\Definition\Object;
    use Hope\Di\Definition\Closure;

    /**
     * Class ReflectionResolver
     *
     * @package Hope\Di\Resolver
     */
    class ReflectionResolver implements IResolver
    {

        /**
         * Resolve definition dependencies
         *
         * @param \Hope\Di\IContainer   $container
         * @param \Hope\Di\IDefinition  $definition
         *
         * @return void
         */
        public function resolve(IContainer $container, IDefinition $definition)
        {
            $reflection = $definition->getReflection();

            if ($definition instanceof Object) {
                // Check reflection instance
                if ($reflection instanceof \ReflectionClass) {
                    // Check class constructor
                    if ($constructor = $reflection->getConstructor()) {
                        $definition->arguments(
                            $this->resolveParameters($constructor->getParameters())
                        );
                    }
                }
            } elseif ($definition instanceof Closure) {
                // Check reflection instance
                if ($reflection instanceof \ReflectionFunction) {
                    $definition->arguments(
                        $this->resolveParameters($reflection->getParameters())
                    );
                }
            }
        }

        /**
         * @param \ReflectionParameter[] $parameters
         *
         * @return array
         */
        protected function resolveParameters(array $parameters)
        {
            return array_map(function (\ReflectionParameter $parameter) {
                if ($parameter->getClass()) {
                    return $parameter->getClass()->getName();
                }
                return $parameter->getName();
            }, $parameters);
        }

    }

}