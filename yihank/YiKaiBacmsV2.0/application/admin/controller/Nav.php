<?php
namespace app\admin\controller;
/**
 * 导航管理
 */

class Nav extends Admin {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '导航管理',
                'description' => '管理网站调用自定义变量',
                ),
            'menu' => array(
                    array(
                        'name' => '导航列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            '_info' => array(
                    array(
                        'name' => '添加导航',
                        'url' => url('info'),
                    ),
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('导航列表'=>url());
        $list=model('Nav')->loadList();
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('_page',$list->render());
        return $this->fetch();
    }
    //信息
    public function info(){
        $nav_id=input('post.nav_id');
        $model = model('Nav');
        if (input('post.')){
            if ($nav_id){
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

            $this->assign('info',$model->getInfo(input('nav_id')));
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $nav_id = input('id');
        if(empty($nav_id)){
            return ajaxReturn(0,'参数不能为空');
        }
        //判断导航下的菜单
        $where = array();
        $where['nav_id'] = $nav_id;
        $contentNum = model('NavMenu')->countList($where);
        if(!empty($contentNum)){
            return ajaxReturn(0,'请先删除该导航下的菜单！');
        }
        $map = array();
        $map['nav_id'] = $nav_id;
        if(model('Nav')->del($nav_id)){
            return ajaxReturn(200,'导航删除成功！');
        }else{
            return ajaxReturn(0,'导航删除失败！');
        }
    }
}

