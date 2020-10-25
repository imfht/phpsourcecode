<?php
/**
 * 打卡记录模型
 *
 * Date: 17-5-6
 * Time: 下午7:31
 * author :李华 yehong0000@163.com
 */

namespace app\work\model;


use think\M;

class WorkRecord extends M
{
    protected static $Obj;
    protected        $table = 'w_work_record';


    /**
     * 初始化
     */
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * @return WorkRecord
     */
    public static function getInstance()
    {
        if (!self::$Obj) {
            self::$Obj = new self;
        }
        return self::$Obj;
    }

    /**
     * 根据条件查询列表
     *
     * @param $where
     * @param $page
     */
    public function getListByWhere($where, $page)
    {
        $page = $page > 0 ? $page : 1;
        return $this->where($where)
            ->page($page)
            ->limit(20)
            ->order('id', 'desc')
            ->select();
    }
}