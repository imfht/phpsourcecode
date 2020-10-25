<?php
/**
 * 菜单管理
 * @author user
 *
 */
class MenusController extends AdminController {
    public function indexAction(){
        // 条件
        $parameters["order"] = "id desc";
        $parameters['limit'] = self::page_size;
        $page = $this->request->getQuery('page', 1);
        $parameters['offset'] = intval($page - 1) * self::page_size;
        $parameters['conditions'] = array(
            'parentid = ?',
            0 
        );
        $rights = SystemMenus::find('all', $parameters);
        $this->view->assign('data', $rights);
        // 加载参数
        $this->view->assign('parameters', $parameters);
    }
    /**
     * 新增,编辑
     */
    public function saveAction(){
        $id = $this->request->getParam('id');
        if($this->request->isPost()){
            // 动作集合
            // 转成字符串
            $postData = array(
                'parentid'=> $this->request->getPost('parentid'),
                'title'=> $this->request->getPost('title'),
                'icon'=> $this->request->getPost('icon'),
                'url'=> $this->request->getPost('url') 
            );
            if($id){
                $rights = SystemMenus::find('first', $id);
                if(!$rights) $this->displayAjax(false, '未找到您要修改的信息');
                $status = $rights->update_attributes($postData);
            }else{
                $rights = new SystemMenus($postData);
                $status = $rights->save();
            }
            if($status == false) $this->displayAjax(false, join($rights->getMessages(), '<br>'));
            $this->displayAjax(true, '', array(
                'redirect_url'=> '/Admin/menus/index' 
            ));
        }
        if($id){
            $data = SystemMenus::find('first', $id);
            $this->view->assign('data', $data);
        }
        // 加载顶级分类
        $this->view->assign('topmenus', SystemMenus::find('all', array(
            'conditions'=> array(
                'parentid=0' 
            ) 
        )));
    }
    /**
     * 删除
     */
    public function delAction(){
        $id = $this->request->getParam('id');
        if($id){
            if(SystemMenus::count(array(
                'conditions'=> array(
                    'parentid = ?',
                    $id 
                ) 
            ))){
                $this->displayAjax(false, '还有下级分类不能删除');
            }
            $roles = SystemMenus::find('first', $id);
            $roles->delete();
        }
        $this->displayAjax(true, '删除成功');
    }
}
