<?php
/**
 * Created by JetBrains PhpStorm.
 * User: taotao
 * Date: 14-5-10
 * Time: 下午11:53
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Controller;

use Admin\Controller\BaseController;

class UserController extends BaseController{
    public function index(){
        $User = D('User'); // 实例化User对象
        $count      = $User->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','首页');
        $Page->setConfig('end','末页');
        //$Page->setConfig('theme', '<ul class="pagination pagination-sm"><li><a> %HEADER%</a></li> <li><a>%FIRST%</a></li> <li><a>%UP_PAGE%</a></li> <li class="active"><a >%LINK_PAGE%</a></li> <li><a>%DOWN_PAGE% %END% </ul>');
        $Page->setConfig('theme', '<ul class="pagination pagination-sm"></li><li>%UP_PAGE%</li> <li class="active"><a>%LINK_PAGE%</a></li> <li><a>%DOWN_PAGE%</a></li> </ul>');

        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $User->order('reg_time')->limit($Page->firstRow.','.$Page->listRows)->select();
        //echo $User->getLastSql();exit;
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }
    public function add(){
        if(IS_POST){
            $Dao = D("User");
            if($Dao->create()){
                $Dao->password = md5($_POST["password"]);
                if($lastInsId = $Dao->add()){
                    $data = array("status"=>1,"info"=>"新增成功");
                    $this->ajaxReturn(json_encode($data));
                } else {
                    $data = array("status"=>0,"info"=>"新增失败");
                    $this->ajaxReturn(json_encode($data));
                }
            }else{
                $data = array("status"=>0,"info"=>$Dao->getError());
                $this->ajaxReturn(json_encode($data));
            }
        }else{
            $this->display();
        }
    }
    public function SetStatus(){
        $user = D("User");
        $id = I("id");
        if(I("id") == 1){
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] =   array('in',$id);
        switch(I("action")){
            case 'open':
                $user-> where($map)->setField('status',1);
                $this->success('启用成功');
                break;
            case 'forbid':
                $user-> where($map)->setField('status',0);
                $this->success('禁用成功');
                break;
            default:
                $this->error('参数非法');
        }
    }
    public function del(){
        $user = D("User");
        $id = I("id");
        if(I("id") == 1){
            $data = array("status"=>0,"info"=>"不允许对超级管理员执行该操作!");
            $this->ajaxReturn(json_encode($data));
        }
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->ajaxReturn("请选择要操作的数据!");
        }
        $map['id'] =   array('in',$id);
        $result = $user->where($map)->delete();
        if($result){
            $data = array("status"=>1,"info"=>"删除成功");
            $this->ajaxReturn(json_encode($data));
        }else{
            $data = array("status"=>0,"info"=>"删除失败");
            $this->ajaxReturn(json_encode($data));
        }
    }
}