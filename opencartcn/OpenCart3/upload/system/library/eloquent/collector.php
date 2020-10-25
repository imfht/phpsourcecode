<?php
/**
 * eloquentcollector.php
 *
 * @copyright  2019 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2019-03-05 09:22
 * @modified   2019-03-05 09:22
 */

namespace Eloquent;

class Collector extends \DebugBar\DataCollector\PDO\PDOCollector
{
    private $capsule;

    public function __construct($capsule)
    {
        parent::__construct();
        $this->capsule = $capsule;
        $this->addConnection($this->getTraceablePdo(), 'Eloquent PDO');
        $this->setRenderSqlWithParams(true, '');
    }

    /**
     * @return TraceablePDO
     */
    protected function getTraceablePdo()
    {
        return new TraceablePDO($this->getEloquentPdo());
    }

    /**
     * @return \PDO
     */
    protected function getEloquentPdo()
    {
        return $this->getEloquentCapsule()->getConnection()->getPdo();
    }

    /**
     * @return \Illuminate\Database\Capsule\Manager;
     */
    protected function getEloquentCapsule()
    {
        return $this->capsule;
    }

    // Override

    public function getName()
    {
        return "eloquent_pdo";
    }

    // Override
    public function getWidgets()
    {
        return array(
            "eloquent" => array(
                "icon" => "inbox",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "eloquent_pdo",
                "default" => "[]"
            ),
            "eloquent:badge" => array(
                "map" => "eloquent_pdo.nb_statements",
                "default" => 0
            )
        );
    }
}