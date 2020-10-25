<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;
/**
 * 推荐位管理
 */

class AdminPosition extends Admin {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '推荐位管理',
                'description' => '管理网站调用自定义变量',
                ),
            'menu' => array(
                    array(
                        'name' => '推荐位列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            '_info' => array(
                    array(
                        'name' => '添加推荐位',
                        'url' => url('info'),
                    ),
                ),
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('推荐位列表'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',model('Position')->loadList());
        return $this->fetch();
    }

    /**
     * 详情
     */
    public function info(){
        $position_id=input('post.position_id');
        $model = model('Position');
        if (input('post.')){
            if ($position_id){
                $status=$model->edit();
            }else{
                $status=$model->add();
            }
            if($status!==false){
                return ajaxReturn(200,'操作成功',url('index'));
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            $this->assign('info',$model->getInfo(input('position_id')));
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $positionId = input('id');
        if(empty($positionId)){
            $this->error('参数不能为空！');
        }
        //获取用户数量
        $map = array();
        $map['position_id'] = $positionId;
        if(model('Position')->del($positionId)){
            return ajaxReturn(200,'推荐位删除成功！');
        }else{
            return ajaxReturn(0,'推荐位删除失败！');
        }
    }
}

