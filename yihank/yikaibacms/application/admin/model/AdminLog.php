<?php
namespace app\admin\model;
use think\Model;
/**
 * 操作记录
 */
class AdminLog extends Model {
    //完成
    protected $auto = ['time', 'ip','app','user_id'];
    protected function setTimeAttr(){
        return time();
    }
    protected function setIpAttr(){
        return get_client_ip();
    }
    protected function setAppAttr(){
        return 'admin';
    }
    protected function setUserIdAttr(){
        return 0;
    }
    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $limit = 0){
        $data = $this->name('admin_log as A')
                ->join('{pre}admin_user as B ON A.user_id = B.user_id')
                ->field('A.*,B.username')
                ->where($where)
                ->limit($limit)
                ->order('A.log_id desc')
                ->select();
        return $data;

    }

    /**
     * 获取数量
     * @return int 数量
     */
    public function countList($where = array()){
        return $this->name('admin_log')
                ->alias('A')
                ->join('admin_user B','A.user_id = B.user_id')
                ->where($where)
                ->count();
    }

    /**
     * 添加信息
     * @param string $log 增加数据
     * @return bool 更新状态
     */
    public function addData($log){
        $data = array();
        $data['content'] = $log;
        if(empty($data)){
            return false;
        }
        //只保留500条数据
        $count = $this->countList();
        if($count>500){
            $this->order('log_id asc')->limit('1')->delete();
        }
        //增加记录
        return $this->save($data);
    }

}
