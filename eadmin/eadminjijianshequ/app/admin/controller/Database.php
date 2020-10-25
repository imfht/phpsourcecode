<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\logic\Database as LogicDatabase;


/**
 * 备份还原
 */
class Database extends AdminBase
{

    /**
     * 备份还原逻辑
     */

    private static $databaseLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$databaseLogic = get_sington_object('databaseLogic', LogicDatabase::class);
    }


    /**
     * 列表
     */
    public function databaseList()
    {

        $this->assign('list', self::$databaseLogic->getDatabaseList());


        return $this->fetch('database_export');
    }

    public function importList()
    {


        $this->assign('list', self::$databaseLogic->importList());


        return $this->fetch('database_import');
    }

    public function optimize()
    {


        $this->jump(self::$databaseLogic->optimize($this->param));

    }

    public function repair()
    {

        $this->jump(self::$databaseLogic->repair($this->param));
    }


    public function import($time = 0, $part = null, $start = null)
    {

        $this->jump(self::$databaseLogic->import($time, $part, $start));
    }

    public function delete($time)
    {

        $this->jump(self::$databaseLogic->deleteBak($time));
    }

    public function export()
    {


        $this->jump(self::$databaseLogic->export($this->param));


    }

    public function download($time)
    {

        $this->jump(self::$databaseLogic->downloadBak($time));
    }
}
