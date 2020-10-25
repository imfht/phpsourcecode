<?php

/**
 * 打卡主记录模型
 *
 * Date: 17-5-6
 * Time: 下午7:11
 * author :李华 yehong0000@163.com
 */
namespace app\work\model;

use think\M;

class Work extends M
{
    protected static $Obj;
    protected        $table = 'w_work';
    private          $rows;

    /**
     * 初始化
     */
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * @return Work
     */
    public static function getInstance()
    {
        if (!self::$Obj) {
            self::$Obj = new self;
        }
        return self::$Obj;
    }

    /**
     * 根据日期索引获取当前用户一条记录
     *
     * @param int $date
     *
     * @return mixed
     */
    public function getRowByDate($date = 0)
    {
        $date = $date ?: date('Ymd');
        if (!isset($this->rows[$date])) {
            $this->rows[$date] = $this->get([
                'date_index' => $date,
                'c_id'       => CID,
                'uid'        => UID
            ]);
        }
        return $this->rows[$date];
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
        $list = $this->where($where)
            ->page($page)
            ->limit(20)
            ->order('id', 'desc')
            ->select();
        return $list;
    }
}