<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */

namespace app\systems\controller;
use app\common\controller\Base;
use think\Cache;
use think\Db;
use think\helper\Hash;
class Ajax extends Base
{

    // public function _initialize() {
           
    // }
    /**
     * [quickEdit 快速编辑]
     * @Author   Jerry
     * @DateTime 2017-05-04
     * @Example  eg:
     * @return   [type]     [description]
     */
    public function quickEdit(){
        $field = input('post.name', '');
        $value = input('post.value', '');
        $table = input('post.table', '');
        $type  = input('post.type', '');
        $id    = input('post.pk', '');
        $validate = input('post.validate', '');
        $validate_fields = input('post.validate_fields', '');

        if ($table == '') return $this->error('缺少表名');
        if ($field == '') return $this->error('缺少字段名');
        if ($id == '') return $this->error('缺少主键值');

        // 验证是否操作管理员
        // if ($table == 'admin_user' || $table == 'admin_role') {
        //     if ($id == 1) {
        //         return $this->error('禁止操作超级管理员');
        //     }
        // }

        // 验证器
        if ($validate != '') {
            $validate_fields = array_flip(explode(',', $validate_fields));
            if (isset($validate_fields[$field])) {
                $result = $this->validate([$field => $value], $validate.'.'.$field);
                if (true !== $result) $this->error($result);
            }
        }

        switch ($type) {
            // 日期时间需要转为时间戳
            case 'combodate':
                $value = strtotime($value);
                break;
            // 开关
            case 'switch':
                $value = $value == 'true' ? 1 : 0;
                break;
            // 开关
            case 'password':
                $value = Hash::make((string)$value);
                break;
        }

        // 主键名
        $pk     = Db::name($table)->getPk();
        $result = Db::name($table)->where($pk, $id)->setField($field, $value);


        if (false !== $result) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
    /**
     * [tpost 公共POST处理]
     * @Author   Jerry
     * @DateTime 2017-04-30
     * @Example  eg:
     * @return   [type]     [description]
     */
    public function tpost(){
        $data = $this->request->param();
        if($data['encode']){
            //解密TABLE和WHERE
            $data['table'] = decode($data['table']); 
        }
        $table = $data['table'];
        $data['uid'] = parent::getUid();
        unset($data['table']);
        if($data[$data['field']]){
           $data['update_date'] = date('Y-m-d H:i:s',time());
            $this->getbase->getedit($table,['where'=>[$data['field']=>$data[$data['field']]]],$data);
           
             $this->success('修改成功',$data['gourl']?$data['gourl']:"");  
        }else{
            $data['create_date'] = date('Y-m-d H:i:s',time());
          if($this->getbase->getadd($table,$data)){
                $this->success('添加成功',$data['gourl']?$data['gourl']:"");
            } 
        }
        

    }
    /**
     * [delete 删除单个]
     * @return [type] [description]
     */
    public function delete(){
         if($this->request->isAJax()&&$this->getuid()>0){
            $data = $this->request->param();
            $wheres = explode("-", decode($data['where']));
            $where[$wheres[0]] =$wheres[1]; 

            model('Base')->getdel(decode($data['table']),['where'=>$where]);
            $this->success('删除成功');
        }
    }
    public function tmkdel(){
      if($this->request->isAJax()){
            $data = $this->request->param();
            $where = "{$data['field']}='{$data[$data['field']]}'";
            model('Base')->getdel($data['table'],['where'=>$where]);
            $this->success('删除成功');
        }  
    }
  /**
   * [tmkedit 公共修改和新加]
   * @return [type] [description]
   */
    public function tmkedit(){
        if($this->request->isAJax()){
            $data = $this->request->param();
             if($data['validate']) $this->validate($data['validate'],'',$data,'ajax');
            if($data[$data['field']]){
                $where = "{$data['field']}='{$data[$data['field']]}'";
                unset($data[$data['field']]);
                $data['update_time'] = date('Y-m-d H:i:s',time());
                model('Base')->getedit($data['table'],['where'=>$where],$data);
                $this->success('更新成功',$data['gourl']);
           }else{
            $data['create_time'] = date('Y-m-d H:i:s',time());
            unset($data[$data['field']]);
            if(model('Base')->getadd($data['table'],$data)){
                $this->success('添加成功',$data['gourl']);
              }else{
                $this->success('添加失败');
              }
           }
    
      }
    }


    public function add(){
            if($this->request->isAJax()){
               $data = $this->request->param();
                if(model('Base')->getadd(decode($data['table']),$data)){
                    $this->success('添加成功',$data['returnurl']);
                }else{
                    $this->error('添加错误');
                } 
            }
            

        }
   
  
}
