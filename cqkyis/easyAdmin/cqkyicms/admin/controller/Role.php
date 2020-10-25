<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/9 9:38
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;





use app\admin\model\RoleModel;


class Role extends Base
{

    protected $title="系统设置";

    public function index(){
        $name = "角色管理";
        $roleModel = new RoleModel();
        $list = $roleModel->listRole();

        $this->assign('list',$list);
       
        $this->assign('name',$name);
        $this->assign('title',$this->title);
        return $this->fetch();
    }


    public function add(){
        $role = new RoleModel();
        if(request()->isPost()){
            $data['rules'] = input('all_rules');
            $data['role_name']=input('role_name');
            $result = $role->add($data);
            if($result['code']==1){

                $this->ky_success($result['msg'],$result['data']);
            }else{

                $this->ky_error($result['msg']);
            }
        }else{
        $name = "添加角色";
        $this->assign('name',$name);
        return $this->fetch();
        }
    }


    public function edit($id){
        $role = new RoleModel();
        if(request()->isPost()){
            $data = input('post.');
            $data['rules']=input('all_rules');
            $data['role_id']=$id;
            $reuslt = $role->edit($data);
            if ($reuslt['code'] == 1) {
                //return json(['code'=>$reuslt['code'],'msg'=>$reuslt['msg'],'data'=>$reuslt['data']]);
                $this->ky_success($reuslt['msg'], $reuslt['data']);
            } else {
                //return json(['msg'=>$reuslt['msg']]);
                $this->ky_error($reuslt['msg']);
            }
        }else{
            $name = "修改角色";
            $list = $role->findById($id);
            $this->assign('vo',$list);
            $this->assign('name',$name);
            return $this->fetch();
        }
    }


    public function del($id){
        $role = new RoleModel();
       $reuslt= $role->del($id);
        if($reuslt['code']==1){
            return json(['code'=>$reuslt['code'],'msg'=>$reuslt['msg'],'data'=>$reuslt['data']]);

        }else{
            return json(['msg'=>$reuslt['msg']]);

        }
    }
}