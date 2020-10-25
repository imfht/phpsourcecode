<?php
namespace app\common\model;

use think\Model;
use think\Db;

class AnnounceArrive extends Model{

    public function getListPage($map,$order='uid asc',$r=30)
    {
        $totalCount=$this->where($map)->count();
        $list=$this->where($map)->order($order)->paginate($r);

        return array($list,$totalCount);
    }

    public function addData($data)
    {
        $res=$this->save($data);
        if($res){
            Db::name('Announce')->where(['id'=>$data['announce_id']])->setInc('arrive');
        }
        return $res;
    }

    /**
     * 设置全部公告到达某人
     * @param int $uid
     * @return bool
     */
    public function setAllArrive($uid=0)
    {
        !$uid&&$uid=is_login();
        if(!$uid){
            $this->error="请先登录！";
            return false;
        }
        $announceModel=Db::name('Announce');
        $map['status']=1;
        $map['end_time']=['gt',time()];

        $announceIds=$announceModel->where($map)->field('id')->limit(999)->select();

        $announceIds=array_column($announceIds,'id');
        if(count($announceIds)){
            $map_arrive['announce_id']=array('in',$announceIds);
            $map_arrive['uid']=$uid;
            $alreadyIds=$this->where($map_arrive)->field('announce_id')->select();
            $alreadyIds=array_column($alreadyIds,'announce_id');
            if(count($alreadyIds)){
                $needIds=array_diff($announceIds,$alreadyIds);
            }else{
                $needIds=$announceIds;
            }
            $dataList=array();
            $data=array('create_time'=>time(),'uid'=>$uid);
            foreach($needIds as $val){
                $data['announce_id']=$val;
                $dataList[]=$data;
            }
            unset($val);
            $res=$this->addAll($dataList);
            if($res){
                $announceModel->where(['id'=>['in',$needIds]])->setInc('arrive');
            }
            return $res;
        }
        $this->error='没有可设置公告！';
        return false;
    }
    
    public function getDataByMap($map)
    {
        $data=$this->where($map)->find();
        return $data;
    }
    /**
     * 获取已读列表
     * @param $map
     * @return mixed
     */
    public function getListByMap($map)
    {
        $list=$this->where($map)->select();
        return $list;
    }
} 