<?php

namespace Cute\Contrib\Command;

use \Cute\Shell\Command;

/**
 * 为数据库生成Model
 * 用法:
 *  ./run.php orm models --dbkey=wordpress --modns='Blog\Model' --singular=y
 */
class ORMCommand extends Command
{

    protected $dbman = null;
    protected $singular = false;
    protected $dbtype = 'mysql';
    protected $dbkey = 'default';
    protected $modns = '';

    protected function setArgs()
    {
        foreach ($this->args as $key => $value) {
            if (is_string($key) && property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    protected function generate($model_dir = '.')
    {
        $this->dbman = $this->app->load($this->dbtype, $this->dbkey)->getManager();
        if (!empty($this->modns)) {
            $this->app->importStrip($this->modns, $model_dir);
        }
        $this->dbman->setSingular(strtolower($this->singular) === 'y');
        $this->dbman->genAllModels($model_dir, $this->modns);
    }

    public function execute()
    {
        $model_dir = 'models';
        if (count($this->args) >= 1) {
            $model_dir = reset($this->args);
            $this->setArgs();
        }
        $this->generate($model_dir);
        $this->app->writeln('Generate models for %s.', $this->modns);
    }

}
