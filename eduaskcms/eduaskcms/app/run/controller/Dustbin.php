<?php
namespace app\run\controller;

use app\common\controller\Run;

class Dustbin extends Run
{
    //初始化 需要调父级方法
    public function initialize(){        
        call_user_func(['parent',__FUNCTION__]); 
    }
    
    //列表 
    public function lists(){
        if(!$this->local['list_fields'])
            $this->local['list_fields'] = [
                'title',
                'model',
                'model_id',
                'data',
                //'status',
                'created',
                //'modified'
            ];
        $this->local['item_actions']['modify']=false;
		$this->local['actions']['create']=false;
        $this->addItemAction('恢复' , array('Dustbin/recover',['id'=>'id'],'parse'=>['id']) , '&#xe609;');		
        return call_user_func(['parent',__FUNCTION__]);
    }
    
    public function recover(){
        $id  = intval($this->args['id']);
        if(!$id ) return  $this->message('error','缺少参数ID');        
        $old_data = $this->mdl->where(['id'=>['id', 'eq', $id]])->find();
        if(empty($old_data)) return  $this->message('error','需要还原的数据不存在'); 
        $recover_model = $this->loadModel($old_data['model']);        
        $exists_data  =  $recover_model->where(['id'=>['id', 'eq',$old_data['model_id']]])->find();        
        if($exists_data) return  $this->message('error','需要还原的对象ID已存在，还原失败');  
            
        $recover_data  = unserialize(@gzuncompress($old_data['data']));        
        $rslt  = $recover_model->isValidate(false)->isUpdate(false)->save($recover_data);
        
        if($rslt){
            $old_data->delete();
            return  $this->message('success','数据已经还原成功',['返回列表'=>['lists']]);  
        }else{
           return  $this->message('error','数据还原失败');    
        }
    }
    
    //添加
    public function create(){        
       return  $this->message('error','不支持该操作'); 
    }
    
    //修改
    public function modify(){        
        return  $this->message('error','不支持该操作'); 
    } 
}