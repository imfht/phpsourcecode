<?php
namespace app\kbcms\controller;
use app\admin\controller\Admin;

/**
 * Class Article 文章栏目控制器类
 * hongkai.wang 20161203  QQ：529988248
 */
class AdminCategory extends Admin{
    /**
     * 当前模块参数
     */
    public function _infoModule() {
        $data = array('info' => array('name' => '栏目管理',
            'description' => '管理网站全部栏目',
        ),
            'menu' => array(
                array('name' => '栏目列表',
                    'url' => url('kbcms/AdminCategory/index'),
                    'icon' => 'list',
                ),
            ),
        );
        $modelList = get_all_service('ContentModel', '');
        if (!empty($modelList)) {
            $i = 0;
            foreach ($modelList as $key => $value) {
                $i++;
                $data['add'][$i]['name'] = '添加' . $value['name'] . '栏目';
                $data['add'][$i]['url'] = url($key . '/AdminCategory/add');
                $data['add'][$i]['icon'] = 'plus';
            }
        }
        return $data;
    }
	//文章栏目列表
	public function index(){
        header("Content-type: text/html; charset=utf-8");
		$where=array();
		$list=model('Category')->loadList($where);
		$this->assign('list',$list);
        $this->assign('type',array('0'=>'频道','1'=>'列表'));
		$this->assign('count',count($list));
		return $this->fetch();
	}
}
