<?php
namespace app\admin\Model;

use think\Model;
use think\Db;

class CountLost extends Model
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 每日执行流失率统计
     * @return bool
     */
    public function lostCount()
    {
        $memberModel = model('admin/Member');
        $map['status'] = 1;
        $totalUser = $memberModel->where($map)->count()*1;

        $date = time_format(time(),'Y-m-d 00:00');
        $lost_long = modC('LOST_LONG',30,'Count');
        $select_date = strtotime($date." - ".$lost_long." day");
        $map['last_login_time'] = array('lt',$select_date);
        $lostUser = $memberModel->where($map)->count()*1;

        $lostRate = $lostUser/$totalUser;

        $map_yesterday['date'] = strtotime($date." - 2 day");
        $yesterdayInfo = $this->getDataByMap($map_yesterday);
        if($yesterdayInfo){
            $data['new_lost'] = $lostUser - $yesterdayInfo['lost_num'];
        }
        $data['date'] = strtotime($date." - 1 day");
        $data['user_num'] = $totalUser;
        $data['lost_num'] = $lostUser;
        $data['rate'] = $lostRate;
        $data['create_time'] = time();

        $have = $this->where('date',$data['date'])->find();
        if(!$have){
            $this->allowField(true)->save($data);
        }
        
        return true;
    }

    /**
     * 根据条件获取数据
     *
     * @param      <type>  $map    The map
     *
     * @return     <type>  The data by map.
     */
    public function getDataByMap($map)
    {
        $data = $this->where($map)->find();

        return $data;
    }

    /**
     * Gets the list by page.
     *
     * @param      <type>   $map    The map
     * @param      string   $order  The order
     * @param      string   $field  The field
     * @param      integer  $r      { parameter_description }
     *
     * @return     <type>   The list by page.
     */
    public function getListByPage($map,$order='create_time desc',$field='*',$r=20)
    {
        $list = $this->where($map)->order($order)->field($field)->paginate($r,false,['query'=>request()->param()]);
        foreach($list as &$val){
            $val['date'] = time_format($val['date'],'Y-m-d');
            $val['rate'] = ($val['rate']*100)."%";
        }
        unset($val);

        return $list;
    }
} 