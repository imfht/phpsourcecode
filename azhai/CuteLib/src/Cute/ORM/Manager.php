<?php

/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\ORM;

use \PDO;
use \Cute\ORM\Database;
use \Cute\Cache\Subject;
use \Cute\Cache\BaseCache;
use \Cute\Utility\Inflect;
use \Cute\View\Templater;

/**
 * 数据库
 */
class Manager
{

    protected $singular = false;
    protected $db = null;
    protected $tables = [];
    protected $subject = null;

    public function __construct(Database& $db, $singular = false)
    {
        $this->setSingular($singular);
        $this->db = $db;
    }

    public function addCache(BaseCache $cache, $timeout = 0)
    {
        if (!$this->subject) {
            $this->subject = new Subject($this->tables, $timeout);
            $this->subject->attach($cache);
            $this->subject->forceRead();
        } else {
            $this->subject->attach($cache);
        }
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getDB()
    {
        return $this->db;
    }

    public function getTableMap()
    {
        return $this->tables ? : [];
    }

    public function setSingular($singular)
    {
        $this->singular = $singular;
        return $this;
    }

    public function loadModel($table)
    {
        if (isset($this->tables[$table])) {
            $ns_model = $this->tables[$table];
            return $this->getDB()->queryModel($ns_model);
        }
    }

    public function addTable($table, &$model, &$ns = '')
    {
        if (empty($model)) {
            $model = $this->singular ? Inflect::singularize($table) : $table;
            $model = Inflect::camelize($model);
        }
        $ns = trim($ns, '\\');
        $ns_model = empty($ns) ? $model : sprintf('\\%s\\%s', $ns, $model);
        $this->tables[$table] = $ns_model;
    }

    public function readFields($table)
    {
        $columns = $this->getDB()->getColumns($table);
        $pkeys = [];
        $fields = [];
        foreach ($columns as $column) {
            if ($column->isPrimaryKey()) {
                $pkeys[] = $column->name;
            }
            $default = $column->default;
            $cate = $column->getCategory();
            if ($cate === 'int') {
                $default = intval($default);
            } else if ($cate === 'float') {
                $default = floatval($default);
            } else if (!$column->isNullable()) {
                if ($cate === 'char') {
                    $default = '';
                } else if ($cate === 'datetime') {
                    $default = '0000-00-00 00:00:00';
                }
            }
            $fields[$column->name] = $default;
        }
        return compact('table', 'pkeys', 'fields');
    }

    public function genModelFile($dir, $table, $model = '', $ns = '')
    {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = $dir . DIRECTORY_SEPARATOR . $model . '.php';
        if (class_exists($ns . '\\' . $model)) {
            return $filename;
        }
        $data = $this->readFields($table);
        $data['name'] = $model;
        $data['ns'] = $ns;
        $data['mixin'] = null;
        $data['behaviors_in_mixin'] = false;
        $mixin = $ns . '\\' . $model . 'Mixin';
        if (trait_exists($mixin, true)) {
            foreach ($data['fields'] as $field => $default) {
                if (property_exists($mixin, $field)) {
                    unset($data['fields'][$field]);
                }
            }
            $data['mixin'] = $mixin;
            $data['behaviors_in_mixin'] = method_exists($mixin, 'getBehaviors');
        }
        $tpl = new Templater(__DIR__);
        $content = $tpl->render('model_tpl.php', $data);
        $content = "<?php\n\n" . trim($content);
        file_put_contents($filename, $content);
        return $filename;
    }

    public function genAllModels($dir, $ns = '')
    {
        $db = $this->getDB();
        $prelen = strlen($db->getPrefix());
        $tables = $db->listTables();
        foreach ($tables as $table) {
            $table = substr($table, $prelen);
            $model = '';
            if (ends_with($table, 'meta')) {
                $model = substr(ucfirst($table), 0, -4) . 'Meta';
            }
            $this->addTable($table, $model, $ns);
            $this->genModelFile($dir, $table, $model, $ns);
        }
        if ($this->subject) {
            $this->subject->write();
        }
    }

}
