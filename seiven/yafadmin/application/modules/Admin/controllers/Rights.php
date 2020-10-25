<?php
/**
 * 权限资源
 * @author user
 *
 */
class RightsController extends AdminController {
    public function indexAction(){
        // 条件
        $parameters["order"] = "id desc";
        $parameters['limit'] = self::page_size;
        $page = $this->request->getQuery('page', 1);
        $parameters['offset'] = intval($page - 1) * self::page_size;
        $parameters['conditions'] = array();
        $rights = SystemRights::find('all', $parameters);
        $this->view->assign('data', $rights);
        // 翻页
        $rowCount = SystemRights::count($parameters['conditions']);
        $paginator = new Paginator($rowCount, self::page_size);
        $this->view->assign('page', $paginator->show());
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
            $actionList = $this->request->getPost('actionList');
            $actionList = array_unique($actionList);
            // 转成字符串
            $postData = array(
                'name'=> $this->request->getPost('rightName'),
                'content'=> join(',', $actionList) 
            );
            if($id){
                $rights = SystemRights::find('first', $id);
                if(!$rights) $this->displayAjax(false, '未找到您要修改的信息');
                $status = $rights->update_attributes($postData);
            }else{
                $rights = new SystemRights($postData);
                $status = $rights->save();
            }
            if($status == false) $this->displayAjax(false, join($rights->getMessages(), '<br>'));
            $this->displayAjax(true, '', array(
                'redirect_url'=> '/Admin/Rights/index'
            ));
        }
        if($id){
            $rights = SystemRights::find('first', $id);
            $this->view->assign('allowList', explode(',', $rights->content));
            $this->view->assign('data', $rights);
        }
        $this->view->assign('controllers', SystemRights::getControllers());
    }
    /**
     * 删除
     */
    public function delAction(){
        $id = $this->request->getParam('id');
        if($id){
            $roles = SystemRights::find('first',$id);
            $roles->delete();
        }
        $this->displayAjax(true, '删除成功');
    }
}
