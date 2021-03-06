<?php

namespace  Bluehouseapp\Bundle\CoreBundle\DependencyInjection\Driver;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractDatabaseDriver implements DatabaseDriverInterface
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $managerName;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var string
     */
    protected $templates;

    public function __construct(ContainerBuilder $container, $prefix, $resourceName, $managerName, $templates = null)
    {
        $this->container = $container;
        $this->prefix = $prefix;
        $this->resourceName = $resourceName;
        $this->managerName = $managerName;
        $this->templates = $templates;
    }

    public function load(array $classes)
    {
        if (isset($classes['controller'])) {
            $this->container->setDefinition(
                $this->getContainerKey('controller'),
                $this->getControllerDefinition($classes['controller'])
            );
        }

        $this->container->setDefinition(
            $this->getContainerKey('repository'),
            $this->getRepositoryDefinition($classes)
        );

        $this->setManagerAlias();
    }

    /**
     * Get the entity manager
     *
     * @return string
     */
    abstract protected function getManagerServiceKey();

    /**
     * Get the doctrine ClassMetadata class
     *
     * @return string
     */
    abstract protected function getClassMetadataClassname();

    /**
     * Get the respository service
     *
     * @param array $classes
     *
     * @return Definition
     */
    abstract protected function getRepositoryDefinition(array $classes);



    /**
     * @param string $class
     *
     * @return Definition
     */
    protected function getControllerDefinition($class)
    {
        $definition = new Definition($class);
        $definition
           ->setArguments(array($this->getConfigurationDefinition()))
            ->addMethodCall('setContainer', array(new Reference('service_container')))
        ;

        return $definition;
    }

    /**
     * @param mixed $models
     *
     * @return Definition
     */
    protected function getClassMetadataDefinition($models)
    {
        $definition = new Definition($this->getClassMetadataClassname());
        $definition
            ->setFactoryService($this->getManagerServiceKey())
            ->setFactoryMethod('getClassMetadata')
            ->setArguments(array($models))
            ->setPublic(false)
        ;

        return $definition;
    }

    protected function setManagerAlias()
    {
        $this->container->setAlias(
            $this->getContainerKey('manager'),
            new Alias($this->getManagerServiceKey())
        );
    }

    /**
     * @param string     $key
     * @param Definition $definition
     */
    protected function setDefinition($key, Definition $definition)
    {
        $this->container->setDefinition($this->getContainerKey($key), $definition);
    }

    /**
     * @param string $key
     * @param string $suffix
     *
     * @return string
     */
    protected function getContainerKey($key, $suffix = null)
    {
        return sprintf('%s.%s.%s%s', $this->prefix, $key, $this->resourceName, $suffix);
    }


    protected function getConfigurationDefinition()
    {
        $definition = new Definition('Bluehouseapp\Bundle\CoreBundle\Controller\Resource\Configuration');
        $definition
            ->setFactoryService('bluehouseapp.controller.configuration_factory')
            ->setFactoryMethod('createConfiguration')
            ->setArguments(array($this->prefix, $this->resourceName, $this->templates))
            ->setPublic(false)
        ;

        return $definition;
    }



}
