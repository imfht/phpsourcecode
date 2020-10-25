<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/10 13:22
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;


use app\admin\model\RoleModel;
use app\admin\model\UserModel;

class User extends Base
{
    protected $title="会员管理";
    public function index(){
        $name = "会员管理";
       if(request()->isAjax()){
           $param = input('param.');
           $limit = $param['pageSize'];
           $offset = ($param['pageNumber'] - 1) * $limit;
           $where = [];
           if (!empty($param['searchText'])) {
               $where['username'] = ['like', '%' . $param['searchText'] . '%'];
           }
            if(!empty($param['deptId'])){
                $where['dept_id']=input('deptId');
            }
            $user = new UserModel();
            $res = $user->getUsersByWhere($where,$offset,$limit);
            foreach($res as $key=>$vo){
                $res[$key]['creattime'] = date('Y-m-d H:i:s', $vo['creattime']);
            }
           $return['total'] = $user->getAllUsers($where);  //总数据
           $return['rows'] = $res;
            $return['sql']=$user->getLastSql();
           return json($return);
        }else{
            $this->assign('name',$name);
            $this->assign('title',$this->title);
            return $this->fetch();
        }
    }


    public function add(){
        $name="添加用户";

        $role = new RoleModel();
        $user = new UserModel();
        if(request()->isPost()){
          $data=input('post.');
            $result=$user->add($data);
            if($result['code']==1){
               
                $this->ky_success($result['msg'],$result['data']);
            }else{

                $this->ky_error($result['msg']);
            }

        }else{
        $res = $role->findByAll();
        $this->assign('role',$res);
        $this->assign([
            'name' => $name,
            'title' => $this->title
        ]);
        return $this->fetch();
        }
    }


    public function dept(){
        return $this->fetch();
    }

    public function edit($id){
        $name="修改用户";
        $role = new RoleModel();
        $user = new UserModel();
        $res = $role->findByAll();
        $userfind = $user->findById($id);
       if(request()->isPost()){
         $data=input('post.');
          $data['uid']=$id;
           $result = $user->edit($data);
         if($result['code']==1){
               $this->ky_success($result['msg'],$result['data']);
           }else{
               $this->ky_error($result['msg']);
           }
  }else{
      $this->assign([
            'name' => $name,
            'title' => $this->title,
            'vo'=>$userfind,
            'role'=>$res,
            'tt'=>json($userfind)
        ]);
        return $this->fetch();
       }
    }



    public function editpwd($id){
        $name="重置密码";
        $user = new UserModel();
        if(request()->isPost()){


            $data=input('post.');
            $data['uid']=$id;
            $sure = $user->editpwd($data);
            if($sure['code']==1){
                $this->ky_success($sure['msg'],$sure['data']);
            }else{
                $this->ky_error($sure['msg']);
            }



        }else{
        $this->assign([
            'name' => $name,
            'title' => $this->title,
            'id'=>$id
        ]);
        return $this->fetch();
        }
    }

    public function batchRemove(){
        $data = input('ids/a');
        $user = new UserModel();
        $res = $user->batchRemove($data);
        return json(['code'=>$res['code'],'data'=>$res['data'],'msg'=>$res['msg']]);

    }

}