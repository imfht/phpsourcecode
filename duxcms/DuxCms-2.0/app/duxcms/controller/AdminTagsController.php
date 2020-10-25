<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 标签管理
 */

class AdminTagsController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '标签管理',
                'description' => '管理网站内容标签',
                ),
            'menu' => array(
                    array(
                        'name' => '标签列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $where = array();
        //查询数据
        $list = target('Tags')->page(20)->loadList($where,$limit);
        $this->pager = target('Tags')->pager;
        $breadCrumb = array('标签列表'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('page',$this->getPageShow($pageMaps));
        $this->adminDisplay();
    }

    /**
     * 批量操作
     */
    public function batchAction(){
        
        $type = request('post.type',0,'intval');
        $ids = request('post.ids');
        if(empty($type)){
            $this->error('请选择操作！');
        }
        if(empty($ids)){
            $this->error('请先选择操作项目！');
        }
        foreach ($ids as $id) {
            switch ($type) {
                case 1:
                    //删除
                    target('duxcms/Tags')->delData($id);
                    break;
            }
        }
        $this->success('批量操作执行完毕！');
    }
    


}

