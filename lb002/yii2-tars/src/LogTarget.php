<?php

namespace Lxj\Yii2\Tars;

use Tars\log\LogServant;
use yii\log\Target;

class LogTarget extends Target
{
    protected $app = 'Undefined';
    protected $server = 'Undefined';
    protected $dateFormat = '%Y%m%d';

    private $logServant;

    public $logConf;
    public $servantName = 'tars.tarslog.LogObj';

    public function init()
    {
        parent::init();

        $this->logServant = new LogServant($this->logConf, $this->servantName);

        $moduleName = $this->logConf->getModuleName();
        $moduleData = explode('.', $moduleName);
        $this->app = $moduleData ? $moduleData[0] : $this->app;
        $this->server = isset($moduleData[1]) ? $moduleData[1] : $this->server;
    }

    public function export()
    {
        $text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";
        $this->logServant->logger($this->app, $this->server, '', $this->dateFormat, [$text]);
    }
}
