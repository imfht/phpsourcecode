<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\Handler;

use \Cute\Cache\RedisDictCache;
use \Cute\Cache\YAMLCache;

trait DBHandler
{

    protected $logger = null;
    protected $dbman = null;
    protected $dbtype = 'mysql';

    public function setup()
    {
        $model_dir = APP_ROOT . '/models';
        if (!empty($this->modns)) {
            $this->app->importStrip($this->modns, $model_dir);
        }
        $tables = $this->getManager()->getTableMap();
        if (empty($tables)) { //找不到表映射数据，从数据库中读取
            $this->dbman->setSingular(true);
            $this->dbman->genAllModels($model_dir, $this->modns);
            $this->dbman->getSubject()->write();
        }
    }

    public function getManager()
    {
        $db = $this->app->load($this->dbtype, $this->dbkey);
        $this->dbman = $db->getManager();
        $cache_key = sprintf('tables_%s_%s', $this->dbtype, $this->dbkey);
        //添加Redis缓存
        $redis = $this->app->load('redis');
        $this->dbman->addCache(new RedisDictCache($redis, $cache_key), 3600);
        //添加Yaml缓存
        $cache_dir = CUTE_ROOT . '/runtime/cache';
        $this->dbman->addCache(new YAMLCache($cache_key, $cache_dir), 3600);
        return $this->dbman;
    }

    public function logSQL()
    {
        if (!$this->logger) {
            $this->logger = $this->app->load('logger', 'sql');
        }
        $sql_rows = $this->dbman->getDB()->getPastSQL();
        foreach ($sql_rows as $row) {
            $this->logger->info('{act} "{sql};"', $row);
        }
    }

    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            $this->$name = $this->dbman->loadModel($name);
        }
        return $this->$name;
    }

}
