<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/10 12:08
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;


use app\admin\model\DeptModel;

class Dept extends Base
{
    protected $title="会员管理";
    public function index(){
        $name = "部门";
        $this->assign('name',$name);
        $this->assign('title',$this->title);

        return $this->fetch();
    }

    public function deptlist(){
        $dept = new DeptModel();
        $list = $dept->listAll();
        return json($list);
    }

    /**
     * 添加
     */

    public function add($id){
        $dept = new DeptModel();
        if(request()->isPost()){
          $data = input('post.');
            $sure = $dept->add($data);
            if($sure['code']=1){
                $this->ky_success($sure['msg'],$sure['data']);
            }else{
                $this->ky_error($sure['msg']);
            }
        }else{
            if($id==0){
                $list=array('dept_id'=>'0','dept_name'=>'根目录');
            }else{
                $list = $dept->findById($id);
            }

            $this->assign('pmenu',$list);
        $name = "添加部门";
        $this->assign('name',$name);
        return $this->fetch();
        }
    }

    public function tree(){
        $dept = new DeptModel();
        $tree = $dept->bulidTree();
        return $tree;
    }

}