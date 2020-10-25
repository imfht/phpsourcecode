<?php
/**
 *
 * Created by PhpStorm.
 * User: root
 * Date: 8/16/16
 * Time: 8:26 PM
 * Vsersion:2.0.0
 */
class AdminuserModel extends CommonModel{
    protected $tableName = 'admin_user';
    /**
     * 获取多条结果集
     * @return mixed
     */
     public function all($keywords){
         $result=[];

         $where['seller_id']=0;
         $where['user_name|email']=array('like',"%{$keywords}%");

         $data=$this->where($where)->order('user_id desc')->select();

         foreach($data as $k=>$val){
             $data[$k]['add_time']=date('Y-m-d H:i:s',$val['add_time']);
             $data[$k]['last_login']=date('Y-m-d H:i:s',$val['last_login']);
             $data[$k]['role_name']=M('adminrole')->show($val['user_id']);
         }

         $result['lists']=$data;
         return $result;
     }
    /**
     * 获取单条结果集
     * @return mixed
     */
    public function show($id){
        return $this->where(['user_id'=>$id])->find();
    }

    /**
     * 删除
     */
    public function destroy($id){
          $admin_info=$this->show($id);
          if($admin_info){
              manage_log('删除了管理员'.$admin_info['user_name']);
              return $this->where(['user_id'=>$admin_info['user_id']])->delete();
          }
          return false;
    }

}