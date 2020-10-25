<?php
/**
 *
 * Created by PhpStorm.
 * User: root
 * Date: 8/16/16
 * Time: 8:26 PM
 * Vsersion:2.0.0
 */
class UserModel extends CommonModel{
    protected $tableName = 'users';
    /**
     * 获取多条结果集
     * @return mixed
     */
     public function all(){
         $result=[];
         $keywords = empty(I('keywords')) ? '' : trim(I('keywords'));
         $start_time=empty(I('start_time')) ? '' : trim(I('start_time'));
         $end_time=empty(I('end_time')) ? '' : trim(I('end_time'));
         $flag=intval(I('flag',-1));//全部类型
         if($flag >=0){
             $where['flag'] = array('eq',$flag);
         }
         if($keywords){
             $where['user_id|user_name|postbox|mobile_phone|pay_points|msn']=array('like',"%{$keywords}%");
         }
         if($start_time){
             $where['reg_time'][]=array('gt',strtotime($start_time));
         }

         if($end_time){
             $where['reg_time'][]=array('lt',strtotime($end_time.' 23:59:59'));
         }

         $count=$this->where($where)->count();
         $filter=$this->page_and_size($count);
         $result['filter']=$filter;

         $data=$this->where($where)->order('user_id desc')->limit($filter['start'],$filter['page_size'])->select();
         foreach($data as $k=>$value){
             $data[$k]['reg_time'] = date('Y-m-d H:i:s',$value['reg_time']);
             /*查看用户对应的增票资质信息*/
             $zizhi_info = $this->table('__ZENGZHISHUI_ZIZHI__')->where(['user_id'=>$value['user_id']])->find();
             $zquer_info = $this->table('__ZQUSER__')->where(['user_id'=>$value['user_id']])->find();
             if($zizhi_info){
                 $data[$k]['sign_zizhi'] = 'edit';
                 $data[$k]['zizhi_id'] = $zizhi_info['zizhi_id'];
             }else{
                 $data[$k]['sign_zizhi'] = 'add';
                 $data[$k]['zizhi_id'] = '';
             }
             /*判断是否是电子签章用户*/
             if($zquer_info){
                 $data[$k]['sign_zquser'] = 'edit';
                 $data[$k]['id'] = $zquer_info['id'];
             }else{
                 $data[$k]['sign_zquser'] = 'add';
                 $data[$k]['id'] = '';
             }

         }
         $result['lists']=$data;

         return $result;
     }

    /**
     * 获取单条结果集
     * @return mixed
     */
    public function show(){
        $user_id = I('user_id',0);
        $data=$this->where(['user_id'=>$user_id])->find();
        return $data;
    }

    /**
     * 删除
     */
    public function destroy(){
        $data=$this->show();
        if($data){
            //记录日志
            manage_log('删除会员:'.$data['user_name']);
            return $this->where(array('user_id'=>$data['user_id']))->delete();
        }
        return false;
    }

    /**
     * 修改
     */
    public function edit(){
        $data = [];
        $user_id = I('user_id',0);
        $data['user_name'] = I('user_name',0);
        $data['mobile_phone'] = I('mobile_phone','');
        $data['postbox'] = I('postbox','');
        $data['password'] = md5(I('password',''));
        return $this->where(['user_id'=>$user_id])->save($data);
    }

    public function export(){
        $data['title']=[
            'user_id'=>'id ',
            'user_name'=>' 用户名 '
        ];
        $data['data']=$this->limit(100)->select();
        return $data;
    }
}