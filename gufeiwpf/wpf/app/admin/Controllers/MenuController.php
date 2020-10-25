<?php
namespace Wpf\App\Admin\Controllers;
use Wpf\App\Admin\Models\AdminMenu;
class MenuController extends \Wpf\App\Admin\Common\Controllers\CommonController{

    public function indexAction(){
        
        $this->headercss
            ->addCss("theme/assets/global/plugins/select2/select2.css")
            ->addCss("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css");
        
        $this->footerjs
            ->addJs("theme/assets/global/plugins/select2/select2.min.js")
            ->addJs("theme/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js")
            ->addJs("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js");
        
        
        $pid = $this->request->getQuery("pid","int",0);
        
        $model = new AdminMenu();
        
		if($pid){
            $data = $model->getInfo($pid)->toArray();        
            $this->view->setVar('data', $data);
		}

        $title = $this->request->getQuery("title","trim");
        
        
        
        $all_menu = array();
        $all_menu_obj = $model->find(array("columns"=>"id,title"));
        
        foreach($all_menu_obj->toArray() as $value){
            $all_menu[$value['id']] = $value['title'];
        }
        $where[] = "pid = {$pid}";
        if($title)
            $where[] = "title like '%{$title}%'";

        $where = implode(" and ",$where);

        $list = $model->find(array(
            'conditions' => $where,
            "order" => "sort asc"
        ))->toArray();

        int_to_string($list,array('hide'=>array(1=>'是',0=>'否'),'is_dev'=>array(1=>'是',0=>'否')));
        if($list) {
            foreach($list as &$key){
                $key['up_title'] = $all_menu[$key['pid']];
            }
            $this->view->setVar('list',$list);
        }
        $this->view->setVar("meta_title","菜单列表");

    }
    
    public function addAction(){
        $model = new AdminMenu();
        if($this->request->isPost()){            
            if($model->save($_POST)){
                $this->success('新增成功', $this->url->get("Menu/index/pid/".$this->request->getQuery("pid","int",0)));
            }
        } else {
            $this->view->setVar('info',array('pid'=>$this->request->getQuery("pid","int",0)));
            $menus = $model->find()->toArray();
            
            $treemodel = new \Wpf\App\Admin\Models\TreeModel();
            
            $menus = $treemodel->toFormatTree($menus);
            
            $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
            $this->view->setVar('Menus', $menus);
            $this->view->setVar("meta_title","新增菜单");
            $this->view->pick("Menu/edit");
        }
    }
    
    public function editAction(){
        $model = new AdminMenu();
        $id = $this->request->getQuery("id","int",0);
        if($this->request->isPost()){
            if($model->save($_POST)){
                $this->success('更新成功', $this->url->get("Menu/index/pid/".$this->request->getPost("pid","int",0)));
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = $model->getInfo($id)->toArray();
            if(false === $info){
                $this->error('获取后台菜单信息错误');
            }
            $menus = $model->find()->toArray();
            $treemodel = new \Wpf\App\Admin\Models\TreeModel();
            $menus = $treemodel->toFormatTree($menus);
            $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
            $this->view->setVar('Menus', $menus);
            
            $this->view->setVar('info', $info);
            $this->view->setVar("meta_title",'编辑后台菜单');
        }
    }
    
    public function delAction(){
        $id = $this->request->get("id");
        
        if(is_array($id)){
            $id = implode(",",array_unique($id));
        }

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        
        $where = "id in ({$id})";
        $model = new AdminMenu();
        
        $list = $model->find($where);
        
        
        foreach($list as $menu){
            if(! $model->cascadeDel($menu->id)){
                $this->error('删除失败！'.$menu->id);
            }
        }
        
        
        $this->success('删除成功');
    }
    
    public function toogleDevAction(){
        $id = $this->request->getQuery("id","int",0);
        $value = $this->request->getQuery("value","int",1);
        
        $model = new AdminMenu();
        
        $info = $model->getInfo($id);
        
        $info->is_dev = $value;
        
        if($info->save()){
            $this->success('更新成功');
        }
    }
    
    public function toogleHideAction($id,$value = 1){
        $id = $this->request->getQuery("id","int",0);
        $value = $this->request->getQuery("value","int",1);
        
        $model = new AdminMenu();
        
        $info = $model->getInfo($id);
        
        $info->hide = $value;
        
        if($info->save()){
            $this->success('更新成功');
        }
    }

}