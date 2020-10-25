<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;
/**
 * 碎片管理
 */

class AdminFragment extends Admin {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '碎片管理',
                'description' => '管理网站调用自定义变量',
                ),
            'menu' => array(
                    array(
                        'name' => '碎片列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            '_info' => array(
                    array(
                        'name' => '添加碎片',
                        'url' => url('info'),
                    ),
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('碎片列表'=>url());
        $list=model('Fragment')->loadList();
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('_page',$list->render());
        return $this->fetch();
    }
    public function info(){
        $fragment_id=input('post.fragment_id');
        $model = model('Fragment');
        if (input('post.')){
            if ($fragment_id){
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
            $info=$model->getInfo(input('fragment_id'));
            if (!empty($info['pics'])){
                $info['pics']=json_decode($info['pics'],true);
            }
            $this->assign('info',$info);
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $fragmentId = input('id');
        if(empty($fragmentId)){
            return ajaxReturn(0,'参数不能为空');
        }
        $map = array();
        $map['fragment_id'] = $fragmentId;
        if(model('Fragment')->del($fragmentId)){
            return ajaxReturn(200,'碎片删除成功！');
        }else{
            return ajaxReturn(0,'碎片删除失败！');
        }
    }
}

