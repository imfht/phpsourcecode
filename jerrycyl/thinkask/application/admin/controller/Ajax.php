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
namespace app\admin\controller;
use app\common\controller\AdminBase;
class Ajax extends AdminBase{
/**
* [saveconfig 保存配置项目]
* @return [type] [description]
*/
public function saveconfig(){
    if($this->request->isAJax()){
        $request = $this->request->param();
        foreach ( $request as $k => $v) {
            //修改
            if($this->getbase->getone('system_setting',['where'=>['varname'=>"$k"]])){
                $data['value'] = serialize($v);
                $this->getbase->getedit('system_setting',['where'=>['varname'=>"$k"]],$data);
            }else{
                //新加
                $data['value'] = serialize($v);
                $data['varname'] = $k;
                $this->getbase->getadd('system_setting',$data);
            }
        }
        cache('system_setting',model('Base')->getall('system_setting',['order'=>'id asc','cache'=>false]));
        $this->success(lang('update_success'),'');
    }
    


 }
/**
 * [testmail 邮件测试]
 * @return [type] [description]
 */
public function testmail(){
    $set = getset('mail_config');
    json(send_mail($set['testmail']));
 }
 //何在用户分组
public function save_group(){
        if($this->request->isAJax()){
            $data = $this->request->param();
            if(!$data['group_name']) $this->error('组名不能为空');
            if($data['question_id']){
                //修改
                $question_id = $data['question_id'];
                unset($data['question_id']);
                model('base')->getedit('users_group',['where'=>['question_id'=>$question_id]],$data);
                $this->success('修改成功',url('admin/user/group'));
            }else{
                //新加
               if(model('base')->getone('users_group',['where'=>["group_name"=>"{$data['group_name']}"]])) $this->error('组名已存在'); 
                    if(model('base')->getadd('users_group',$data)){
                    $this->success('添加成功',url('admin/user/group'));
                }else{
                    $this->error('服务器异常，请稍后再试');
                }
            }
        }

}


//保存导航菜单
public function save_nv_menu_index(){
        if($this->request->isAJax()){
            $data = $this->request->param();
            if(!$data['title']) $this->error('导航名不能为空');
            if($data['id']){
                //修改
                $id = $data['id'];
                unset($data['id']);
                model('base')->getedit('nv_index',['where'=>['id'=>$id]],$data);
                $this->success('修改成功',url('admin/nv/index',['catid'=>$data['catid']]));
            }else{
                //新加
               if(model('base')->getone('nv_index',['where'=>["title"=>"{$data['title']}",'catid'=>"{$data['catid']}"]])) $this->error('导航名已存在'); 
                    if(model('base')->getadd('nv_index',$data)){
                    $this->success('添加成功',url('admin/nv/index',['catid'=>$data['catid']]));
                }else{
                    $this->error('服务器异常，请稍后再试');
                }
            }
        }

    }


//保存导航分类
public function save_cat_nv_menu_index(){
        if($this->request->isAJax()){
            $data = $this->request->param();
            if(!$data['title']) $this->error('分类名不能为空');
            if($data['id']){
                //修改
                $id = $data['id'];
                unset($data['id']);
                model('base')->getedit('nv_index_cat',['where'=>['id'=>$id]],$data);
                $this->success('修改成功',url('admin/nv/catlist'));
            }else{
                //新加
               if(model('base')->getone('nv_index_cat',['where'=>["title"=>"{$data['title']}"]])) $this->error('分类名已存在'); 
                    if(model('base')->getadd('nv_index_cat',$data)){
                    $this->success('添加成功',url('admin/nv/catlist'));
                }else{
                    $this->error('服务器异常，请稍后再试');
                }
            }
        }

    }
    //保存勾子
    public function save_hook(){
        if($this->request->isAJax()){
            $data = $this->request->param();
            if(!$data['name']) $this->error('勾子名不能这空');
                //转成数据，如果能转成数组，说明有多个，不能转成数组，说明只有一个
            $data['addons'] = implode(",", $data['addons'])?implode(",", $data['addons']):$data['addons'];
            if($data['id']){
                //修改
                $id = $data['id'];
                unset($data['id']);
                model('base')->getedit('hooks',['where'=>['id'=>$id]],$data);
                $this->success('修改成功',url('admin/addons/hooks'));
            }else{
                //新加
               if(model('base')->getone('hooks',['where'=>["name"=>"{$data['name']}"]])) $this->error('勾子已存在'); 
                    if(model('base')->getadd('hooks',$data)){
                    $this->success('添加成功',url('admin/addons/hooks'));
                }else{
                    $this->error('服务器异常，请稍后再试');
                }
            }
        }
    }

}
