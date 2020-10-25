<?php
use ActiveRecord\Config;
class ConfigsController extends AdminController {
    public function indexAction(){
        // 条件
        $parameters["order"] = "id desc";
        $parameters['limit'] = self::page_size;
        $page = $this->request->getQuery('page', 1);
        $parameters['offset'] = intval($page - 1) * self::page_size;
        $parameters['conditions'] = array();
        $data = Configs::find('all', $parameters);
        $this->view->assign('data', $data);
        // 翻页
        $rowCount = Configs::count($parameters);
        $paginator = new Paginator($rowCount, self::page_size);
        $this->view->assign('page', $paginator->show());
        // 加载参数
        $this->view->assign('parameters', $parameters);
    }
    public function saveAction(){
        $id = $this->request->getParam('id');
        if($id){
            $data = Configs::find('first', $id);
            $this->view->assign('data', $data);
        }
        if($this->request->isPost()){
            // 动作集合
            // 转成字符串
            $postData = array(
                'name'=> $this->request->getPost('name'),
                'key'=> $this->request->getPost('key'),
                'value'=> $this->request->getPost('value') 
            );
            if($id){
                if(!$data) $this->displayAjax(false, '未找到您要修改的信息');
                $status = $data->update_attributes($postData);
            }else{
                $data = new Configs($postData);
                $status = $data->save();
            }
            if($status == false) $this->displayAjax(false, join($rights->getMessages(), '<br>'));
            $this->displayAjax(true, '', array(
                'redirect_url'=> '/Admin/configs/index' 
            ));
        }
    }
    
    /**
     * 删除
     */
    public function delAction(){
        $id = $this->request->getParam('id');
        if($id){
            $data = Configs::find('first', $id);
            $data->delete();
        }
        $this->displayAjax(true, '删除成功');
    }
}
