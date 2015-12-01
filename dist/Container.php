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

    use Hope\Di\Definition\Object;
    use Hope\Di\Definition\Closure;

    /**
     * Class Container
     *
     * @package Hope\Di
     */
    class Container implements ContainerInterface
    {

        /**
         * Container values
         *
         * @var array
         */
        protected $_items = [];

        /**
         * Definition factory
         *
         * @var \Hope\Di\Factory
         */
        protected $_factory;

        /**
         * Definition builder
         *
         * @var \Hope\Di\Builder
         */
        protected $_builder;

        /**
         * Definition resolvers
         *
         * @var \SplObjectStorage|Resolver[]
         */
        protected $_resolvers;

        /**
         * Instances
         *
         * @var array
         */
        protected $_instances = [];

        /**
         * Definitions
         *
         * @var array
         */
        protected $_definitions = [];


        /**
         * Instantiate Container
         *
         * @param array            $values  [optional]
         * @param \Hope\Di\Factory $factory
         * @param \Hope\Di\Builder $builder [optional]
         */
        public function __construct(array $values = [], Factory $factory = null, Builder $builder = null)
        {
            if ($factory === null) {
                $factory = new Factory\SimpleFactory();
            }
            // Register factory
            $this->setFactory($factory);

            if ($builder === null) {
                $builder = new Builder\SimpleBuilder();
            }
            // Register builder
            $this->setBuilder($builder);

            // Register simple resolver
            $this->setResolver(new Resolver\SimpleResolver());

            $this->add('container', $this);
            $this->add('Hope\Di\Container', $this);
            $this->add('Hope\Di\ContainerInterface', $this);
        }

        /**
         * Register value
         *
         * @param $name
         * @param $value
         *
         * @return Definition|Definition\Object|Definition\Closure
         */
        public function add($name, $value = null)
        {
            if ($value === null) {
                $value = $name;
            }

            if (is_object($value)) {
                $this->_instances[$name] = $value;
                return;
            } else {
                return $this->_definitions[$name] = $this->_factory->define($this, $name, $value);
            }
            throw new \InvalidArgumentException('Can\'t add value to container');
        }

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
        public function get($name, $throw = true)
        {
            if (is_string($name) && array_key_exists($name, $this->_instances)) {
                return $this->_instances[$name];
            }

            $definition = $this->getDefinition($name);
            $instance = $this->build($definition);

            if ($definition->getScope() === Scope::SINGLETON) {
                $this->_instances[$name] = $instance;
            }

            return $instance;
        }

        public function has($name)
        {
            return is_string($name) && (array_key_exists($name, $this->_instances) || array_key_exists($name, $this->_definitions));
        }

        /**
         * Set definition factory
         *
         * @param \Hope\Di\Factory $factory
         *
         * @return \Hope\Di\Container
         */
        public function setFactory(Factory $factory)
        {
            $this->_factory = $factory;
            return $this;
        }

        /**
         * Returns definition factory
         *
         * @return \Hope\Di\Factory
         */
        public function getFactory()
        {
            return $this->_factory;
        }

        /**
         * Set definitions builder
         *
         * @param \Hope\Di\Builder $builder
         *
         * @return \Hope\Di\Container
         */
        public function setBuilder(Builder $builder)
        {
            $this->_builder = $builder;
            return $this;
        }

        /**
         * Returns definitions builder
         *
         * @return \Hope\Di\Builder
         */
        public function getBuilder()
        {
            return $this->_builder;
        }

        /**
         * Set dependency resolver
         *
         * @param \Hope\Di\Resolver $resolver
         *
         * @return \Hope\Di\Container
         */
        public function setResolver(Resolver $resolver)
        {
            $this->_resolvers = new \SplObjectStorage();
            $this->_resolvers->attach($resolver);
            return $this;
        }

        /**
         * Add dependency resolver
         *
         * @param \Hope\Di\Resolver $resolver
         *
         * @return \Hope\Di\Container
         */
        public function addResolver(Resolver $resolver)
        {
            if ($this->_resolvers->contains($resolver)) {
                throw new \InvalidArgumentException('Resolver already attached');
            }
            $this->_resolvers->attach($resolver);
            return $this;
        }

        /**
         * Return definition
         *
         * @param string $name
         *
         * @return Definition
         */
        protected function getDefinition($name)
        {
            if (array_key_exists($name, $this->_definitions)) {
                return $this->_definitions[$name];
            }
            throw new \InvalidArgumentException("Definition for value {$name} not found in container");
        }

        /**
         * @param \Hope\Di\Definition $definition
         *
         * @return mixed
         */
        protected function build(Definition $definition)
        {
            foreach ($this->_resolvers as $resolver) {
                $resolver->resolve($this, $definition);
            }
            return $this->_builder->build($this, $definition);
        }

    }

}