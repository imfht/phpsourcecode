<?php
/**
 *错误日志
 * Created by PhpStorm.
 * User: root
 * Date: 8/16/16
 * Time: 8:26 PM
 * Vsersion:2.0.0
 */
class ErrorModel extends CommonModel{
    protected $tableName = 'error_log';
    /**
     * 获取多条结果集
     * @return mixed
     */
     public function all($keywords){
         $result=[];

         $where['error_name']=array('like',"%{$keywords}%");

         $count=$this->where($where)->count();

         $filter=$this->page_and_size($count);
         $result['filter']=$filter;

         $data=$this->where($where)->order('id desc')->select();

         $result['lists']=$data;
         return $result;
     }
    /**
     * 获取单条结果集
     * @return mixed
     */
    public function show($id){
        return $this->where(['error_id'=>$id])->find();
    }

    /**
     * 删除
     */
    public function destroy($id){
          $info=$this->show($id);
          if($info){
              manage_log('删除了'.$info['error_name']);
              return $this->where(['error_id'=>$info['error_id']])->delete();
          }
          return false;
    }

}
