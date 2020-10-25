<?php
namespace app\admin\controller;

use think\Controller;
//use think\db\Query;
use think\facade\Config;

class Database extends Common
{
    public function initialize() {
        parent::initialize();
    }

    /**
     * @Title: index
     * @Description: todo(数据库列表)
     * @author 心中有你
     * @date 2018年1月15日
     * @throws
     */
    public function index() {
        $dataList = db()->query("SHOW TABLE STATUS");
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    /**
     * @Title: backup
     * @Description: todo(备份数据库)
     * @author 心中有你
     * @date 2018年1月15日
     * @throws
     */
    public function backup() {
        if (request()->isPost()){
            $id = input('id');
            if (isset($id) && !empty($id)){
                $table_arr = explode(',', $id);   //备份数据表
                $sql = new \expand\Baksql(Config::pull('database'));
                $res = $sql->backup($table_arr);
                return ajaxReturn($res, url('index'));
            }
        }
    }

    /**
     * @Title: reduction
     * @Description: todo(备份列表)
     * @author 心中有你
     * @date 2018年1月15日
     * @throws
     */
    public function reduction() {
        $sql = new \expand\Baksql(Config::pull('database'));
        $dataList = $sql->get_filelist();
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    /**
     * @Title: restore
     * @Description: todo(还原数据库)
     * @author 心中有你
     * @date 2018年1月15日
     * @throws
     */
    public function restore() {
        if (request()->isPost()){
            $name = input('id');
            $sql = new \expand\Baksql(Config::pull('database'));
            $res = $sql->restore($name);
            return ajaxReturn($res, url('reduction'));
        }
    }

    /**
     * @Title: dowonload
     * @Description: todo(下载备份)
     * @author 心中有你
     * @date 2018年1月15日
     * @throws
     */
    public function dowonload() {
        $table = input('table');
        $sql = new \expand\Baksql(Config::pull('database'));
        $sql->downloadFile($table);
    }

    /**
     * @Title: delete
     * @Description: todo(删除备份)
     * @author 心中有你
     * @date 2018年1月15日
     * @throws
     */
    public function delete() {
        if (request()->isPost()){
            $name = input('id');
            $sql = new \expand\Baksql(Config::pull('database'));
            $res = $sql->delfilename($name);
            return ajaxReturn($res, url('reduction'));
        }
    }
}