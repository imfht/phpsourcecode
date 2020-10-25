<?php
/**
 * ORM
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/4/28
 * Time: 16:32
 */

namespace Bjask;


class Doctrine
{
    private $em = null;

    public function create(array $config)
    {
        $oc = new \Doctrine\ORM\Configuration();
        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $oc->setMetadataCacheImpl($cache);
        $oc->setQueryCacheImpl($cache);
        $oc->setResultCacheImpl($cache);
        $oc->setMetadataDriverImpl($oc->newDefaultAnnotationDriver(APP_NAME . '\\Models\\Entities', false));
        $oc->setProxyDir(APP_NAME . '\\Models\\Proxies');
        $oc->setProxyNamespace('Proxies');
        $oc->setAutoGenerateProxyClasses(true);
        $oc->setClassMetadataFactoryName('Doctrine\\ORM\\Mapping\\ClassMetadataFactory');
        $oc->setDefaultRepositoryClassName('Doctrine\\ORM\\EntityRepository');
        $oc->setNamingStrategy(new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy());
        $oc->setQuoteStrategy(new \Doctrine\ORM\Mapping\DefaultQuoteStrategy());
        $connection = \Doctrine\DBAL\DriverManager::getConnection($config, $oc);
        $this->em = \Doctrine\ORM\EntityManager::create($connection, $oc);
    }

    public function getManager()
    {
        return $this->em;
    }
}