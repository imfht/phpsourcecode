<?php
/**
 * 角色分组
 * @author user
 *
 */
class RolesController extends AdminController {
    public function indexAction(){
        // 条件
        $parameters['limit'] = self::page_size;
        $page = $this->request->getParam('page', 1);
        $parameters['offset'] = intval($page - 1) * self::page_size;
        $parameters['conditions'] = array();
        $data = SystemGroups::find('all', $parameters);
        $this->view->assign('data', $data);
        // 翻页
        $rowCount = SystemGroups::count($parameters['conditions']);
        $paginator = new Paginator($rowCount, self::page_size);
        $this->view->assign('page', $paginator->show());
        // 加载参数
        $this->view->assign('parameters', $parameters);
    }
    public function saveAction(){
        $gid = $this->request->getParam('gid');
        if($gid){
            $group = SystemGroups::find('first', $gid);
            $this->view->assign('groupRight', explode(',', $group->rightlist));
            $this->view->assign('data', $group);
        }
        if($this->request->isPost()){
            $rights = $this->request->getPost('rights');
            $postData = array(
                'parent_id'=> $this->request->getPost('parent_id'),
                'gname'=> $this->request->getPost('gname'),
                'rightlist'=> join(',', array_unique($rights?$rights:array())) 
            );
            if($gid){
                if(!$group) $this->displayAjax(false, '未找到您要修改的信息');
                unset($postData['gname']);
                $status = $group->update_attributes($postData);
            }else{
                $group = new SystemGroups($postData);
                $status = $group->save();
            }
            if($status == false) $this->displayAjax(false, join($group->getMessages(), '<br>'));
            $this->displayAjax(true, '', array(
                'redirect_url'=> '/Admin/Roles/index' 
            ));
        }
        // 加载所有分组
        $this->view->assign('groupList', SystemGroups::find('all'));
        // 加载所有权限资源
        $rightData = SystemRights::find('all');
        $rightArray = array();
        $rightUndefined = array();
        foreach($rightData as $key => $item){
            preg_match('/\[.*?\]/', $item->name, $localPre);
            if(isset($localPre[0])){
                $arrayKey = trim($localPre[0], '[]');
                $rightArray[$arrayKey][] = array(
                    'id'=> $item->id,
                    'name'=> $item->name 
                );
            }else{
                $rightUndefined[] = $item;
            }
        }
        $this->view->assign('rightArray', $rightArray); // []中匹配正确的权限资源
        $this->view->assign('rightUndefined', $rightUndefined); // 未被定义的权限资源
    }
    public function delAction(){
        $gid = $this->request->getParam('gid');
        if($gid && $gid !== 1){
            $group = SystemGroups::first($gid);
            $group->delete();
        }
        $this->displayAjax(true, '删除成功');
    }
}
