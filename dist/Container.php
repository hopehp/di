<?php
/**
 * Hope - PHP 7 framework
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
         * Links
         *
         * @var array
         */
        protected $_links = [];

        /**
         * Instances
         *
         * @var array
         */
        protected $_values = [];

        /**
         * Container parent
         *
         * @var \Hope\Di\IContainer
         */
        protected $_parent;

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
         * Definitions
         *
         * @var IDefinition[]
         */
        protected $_definitions = [];

        /**
         * Instantiate Container
         *
         * @param array             $values  [optional]
         * @param \Hope\Di\IFactory $factory [optional]
         * @param \Hope\Di\IBuilder $builder [optional]
         */
        public function __construct(array $values = [], IFactory $factory = null, IBuilder $builder = null)
        {
            if ($factory) {
                $this->setFactory($factory);
            }
            if ($builder) {
                $this->setBuilder($builder);
            }

            // Register simple resolver
            $this->setResolver(new SimpleResolver());

            $this->set(\Hope\Di\IContainer::class, $this);
        }

        /**
         * Register value
         *
         * @param string $name
         * @param $value
         *
         * @return void
         */
        public function set($name, $value)
        {
            if (false === is_string($name)) {
                throw new \InvalidArgumentException(sprintf('Name argument must be a string %s given', gettype($name)));
            }
            if (array_key_exists($name, $this->_values)) {
                throw new \RuntimeException(sprintf('Key %s already defined in container', $name));
            }

            $this->_values[$name] = $value;
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
            if ($name instanceof IDefinition) {
                $name = $name->getName();
            }

            if (is_string($name) && array_key_exists($name, $this->_values)) {
                return $this->_values[$name];
            }

            if (is_string($name) && array_key_exists($name, $this->_definitions)) {

                $definition = $this->_definitions[$name];

                if ($definition->getIsolated() && false === $this->isolated()) {
                    throw new \RuntimeException('Can\'t create isolated definition in not isolated container');
                }

                // Build value
                $value = $this->build($definition);

                // Check definition
                if ($definition->getScope() === Scope::SINGLETON) {
                    $this->_values[$name] = $value;
                }

                return $value;

            } else if ($this->isolated()) {
                return $this->parent()->get($name, $throw);
            } else if (is_string($name)) {
                // Auto wiring
                if (class_exists($name)) {
                    $this->define($name);
                } else if (interface_exists($name, true)) {
                    $class = null;
                    foreach (get_declared_classes() as $klass) {
                        if (in_array($name, class_implements($klass, true))) {
                            $class = $klass;
                            break;
                        }
                    }
                    if ($class) {
                        $this->define($name, $class);
                    } else {
                        throw new \RuntimeException(sprintf('Can\'t find %s interface implementations', $name));
                    }
                }
                // TODO: Recursion. Be careful
                return $this->get($name);
            }
            if ($throw) {
                throw new \RuntimeException(sprintf('Value %s not found in container', $name));
            }
            return false;
        }

        /**
         * Returns `true` if value is registered
         *
         * @param string $name
         *
         * @return bool
         */
        public function has($name)
        {
            return is_string($name) && (
                array_key_exists($name, $this->_values) || array_key_exists($name, $this->_definitions)
            );
        }

        public function invoke(callable $callable, array $locals = [])
        {
            $definition = $this->getFactory()->define($this, '', $callable);

            return $this->build($definition);
        }
        
        /**
         * @inheritdoc
         * @return \Hope\Di\IDefinition|\Hope\Di\Definition\Closure|\Hope\Di\Definition\Object
         */
        public function define($name, $value = null)
        {
            if ($value === null) {
                $value = $name;
            }
            return $this->_definitions[$name] = $this->getFactory()->define($this, $name, $value);
        }

        public function build(IDefinition $definition)
        {
            // TODO : Check definition is already resolved
            // Resolve dependencies
            if ($this->_resolvers->count()) {
                foreach ($this->_resolvers as $resolver) {
                    $resolver->resolve($this, $definition);
                }
            }
            // Build value
            return $this->getBuilder()->build($this, $definition);
        }

        /**
         * Set or get container parent
         *
         * @param \Hope\Di\IContainer $container
         *
         * @return \Hope\Di\IContainer
         */
        public function parent(IContainer $container = null)
        {
            if ($container) {
                $this->_parent = $container;
            }
            return $this->_parent;
        }

        /**
         * @inheritdoc
         */
        public function isolate()
        {
            $isolated = new Container([], $this->getFactory(), $this->getBuilder());
            $isolated->parent($this);

            return $isolated;
        }

        /**
         * @inheritdoc
         */
        public function isolated()
        {
            return $this->_parent !== null;
        }

        /**
         * @inheritdoc
         */
        public function register(IProvider $provider)
        {
            $provider->register($this);
            return $this;
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
            if ($this->_factory === null) {
                $this->_factory = new SimpleFactory();
            }
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

    }

}