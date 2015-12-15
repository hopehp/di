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

    use Hope\Di\Factory\SimpleFactory;
    use Hope\Di\Builder\SimpleBuilder;
    use Hope\Di\Resolver\SimpleResolver;

    /**
     * Class Container
     *
     * @package Hope\Di
     */
    class Container implements IContainer
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
         * @var \Hope\Di\IFactory
         */
        protected $_factory;

        /**
         * Definition builder
         *
         * @var \Hope\Di\IBuilder
         */
        protected $_builder;

        /**
         * Definition resolvers
         *
         * @var \SplObjectStorage|IResolver[]
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
         * @param array             $values  [optional]
         * @param \Hope\Di\IFactory $factory
         * @param \Hope\Di\IBuilder $builder [optional]
         */
        public function __construct(array $values = [], IFactory $factory = null, IBuilder $builder = null)
        {
            if ($factory === null) {
                $factory = new SimpleFactory();
            }
            // Register factory
            $this->setFactory($factory);

            if ($builder === null) {
                $builder = new SimpleBuilder();
            }
            // Register builder
            $this->setBuilder($builder);

            // Register simple resolver
            $this->setResolver(new SimpleResolver());

            $this->add('container', $this);
            $this->add('Hope\Di\Container', $this);
            $this->add('Hope\Di\IContainer', $this);
        }

        /**
         * Register value
         *
         * @param $name
         * @param $value
         *
         * @return IDefinition|Definition\Object|Definition\Closure
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
                return $this->_definitions[$name] = $this->getFactory()->define($this, $name, $value);
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
         * @inheritdoc
         */
        public function make($class, array $locals)
        {

        }

        /**
         * @inheritdoc
         */
        public function call(callable $closure, array $locals)
        {

        }


        /**
         * Set definition factory
         *
         * @param \Hope\Di\IFactory $factory
         *
         * @return \Hope\Di\Container
         */
        public function setFactory(IFactory $factory)
        {
            $this->_factory = $factory;
            return $this;
        }

        /**
         * Returns definition factory
         *
         * @return \Hope\Di\IFactory
         */
        public function getFactory()
        {
            return $this->_factory;
        }

        /**
         * Set definitions builder
         *
         * @param \Hope\Di\IBuilder $builder
         *
         * @return \Hope\Di\Container
         */
        public function setBuilder(IBuilder $builder)
        {
            $this->_builder = $builder;
            return $this;
        }

        /**
         * Returns definitions builder
         *
         * @return \Hope\Di\IBuilder
         */
        public function getBuilder()
        {
            if ($this->_builder === null) {
                $this->_builder = new SimpleBuilder();
            }
            return $this->_builder;
        }

        /**
         * Set dependency resolver
         *
         * @param \Hope\Di\IResolver $resolver
         *
         * @return \Hope\Di\Container
         */
        public function setResolver(IResolver $resolver)
        {
            $this->_resolvers = new \SplObjectStorage();
            $this->_resolvers->attach($resolver);
            return $this;
        }

        /**
         * Add dependency resolver
         *
         * @param \Hope\Di\IResolver $resolver
         *
         * @return \Hope\Di\Container
         */
        public function addResolver(IResolver $resolver)
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
         * @return IDefinition
         */
        protected function getDefinition($name)
        {
            if (array_key_exists($name, $this->_definitions)) {
                return $this->_definitions[$name];
            }
            throw new \InvalidArgumentException("Definition for value {$name} not found in container");
        }

        /**
         * @param \Hope\Di\IDefinition $definition
         *
         * @return mixed
         */
        protected function build(IDefinition $definition)
        {
            foreach ($this->_resolvers as $resolver) {
                $resolver->resolve($this, $definition);
            }
            return $this->_builder->build($this, $definition);
        }

    }

}