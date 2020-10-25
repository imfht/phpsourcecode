<?php


namespace  Bluehouseapp\Bundle\CoreBundle\DependencyInjection\Driver;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;


class DoctrineORMDriver extends AbstractDatabaseDriver
{


    /**
     * {@inheritdoc}
     */
    protected function getRepositoryDefinition(array $classes)
    {
        $repositoryKey = $this->getContainerKey('repository', '.class');
        $repositoryClass = 'Bluehouseapp\Bundle\CoreBundle\Doctrine\ORM\EntityRepository';

        if ($this->container->hasParameter($repositoryKey)) {
            $repositoryClass = $this->container->getParameter($repositoryKey);
        }

        if (isset($classes['repository'])) {
            $repositoryClass = $classes['repository'];
        }

        $definition = new Definition($repositoryClass);
        $definition->setArguments(array(
            new Reference($this->getContainerKey('manager')),
            $this->getClassMetadataDefinition($classes['model'])
        ));

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceKey()
    {
        return sprintf('doctrine.orm.%s_entity_manager', $this->managerName);
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return 'Doctrine\\ORM\\Mapping\\ClassMetadata';
    }
}
