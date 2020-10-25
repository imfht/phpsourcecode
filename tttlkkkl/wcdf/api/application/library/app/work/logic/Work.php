<?php

/**
 *
 * Date: 17-5-6
 * Time: 下午7:11
 * author :李华 yehong0000@163.com
 */
namespace app\work\logic;

use db\Db;
use log\Log;
use app\work\model\Work as Model;

class Work
{
    static protected $Obj;
    /**
     * @var \app\work\model\Work
     */
    protected $Model;

    private function __construct()
    {
        $this->Model = Model::getInstance();
    }


    /**
     * @return Work
     */
    static public function getInstance()
    {
        if (!self::$Obj) {
            self::$Obj = new self;
        }
        return self::$Obj;
    }

    /**
     * 更新主记录
     *
     * @param $data
     *
     * @return false|int
     */
    public function save($data)
    {
        $row = $this->Model->getRowByDate();
        $insertData = [
            'c_id'            => CID,
            'uid'             => UID,
            'date_index'      => date('Ymd'),
            'has_late'        => (isset($row['has_late']) ? $row['has_late'] : 0) || $data['is_late'],
            'has_leave_early' => (isset($row['has_leave_early']) ? $row['has_leave_early'] : 0) || $data['is_leave_early'],
            'has_absenteeism' => (isset($row['has_absenteeism']) ? $row['has_absenteeism'] : 0) || $data['is_absenteeism'],
            'update_time'     => date('Y-m-d H:i:s')
        ];
        if ($row) {
            return $this->Model->save($insertData, $row['id']) ? $row['id'] : 0;
        } else {
            return $this->Model->save($insertData);
        }
    }

    /**
     * 获取打卡记录
     *
     * @param $where
     */
    public function getList($where)
    {
        $startDate = isset($where['startDate']) ? date('Ymd', strtotime($where['startDate'])) : date('Ymd');
        $endDate = isset($where['endDate']) ? date('Ymd', strtotime($where['endDate'])) : date('Ymd');
        $page = isset($where['page']) ? $where['page'] : 1;
        $map = [];
        if ($startDate == $endDate) {
            $map['date_index'] = $startDate;
        } else {
            $map['date_index'] = ['>', $startDate];
            $map['date_index'] = ['<', $endDate];
        }
        $list = $this->Model->getListByWhere($where, $page);
        $userIds = array_unique(array_column($list, 'uid'));
        if ($userIds) {
            $userInfo = Db::table('w_member')
                ->where([
                    'id'   => ['in', $userIds],
                    'c_id' => CID
                ])->field('id,name')->select();
            $userInfo = array_column($userInfo, 'name', 'id');
        } else {
            $userInfo = [];
        }
        foreach ($list as $key => $val) {
            $list[$key]['date'] = sprintf('%s-%s-%s', substr($val['date_index'], 0, 4), substr($val['date_index'], 4, 2), substr($val['date_index'], 6));
            $list[$key]['userName'] = isset($userInfo[$val['uid']]) ? $userInfo[$val['uid']] : '--';
            $list[$key]['hasLate'] = $val['has_late'] > 0 ? '有' : '没有';
            $list[$key]['hasLeaveEarly'] = $val['has_leave_early'] > 0 ? '有' : '没有';
            $list[$key]['hasAbsenteeism'] = $val['has_absenteeism'] > 0 ? '有' : '没有';
        }
        return $list ?: null;
    }
}