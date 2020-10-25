<?php
namespace Wpf\App\Admin\Controllers;
class AdminconfigController extends \Wpf\App\Admin\Common\Controllers\CommonController{
    public $_model;
    
    public function initialize(){
        parent::initialize();
    }
    
    public function onConstruct(){
        parent::onConstruct();
        $this->_model = new \Wpf\Common\Models\Config();
        
    }
    
    public function indexAction(){
        
        $this->headercss
            ->addCss("theme/assets/global/plugins/select2/select2.css")
            ->addCss("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css");
        
        $this->footerjs
            ->addJs("theme/assets/global/plugins/select2/select2.min.js")
            ->addJs("theme/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js")
            ->addJs("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js");
            
        $list = $this->_model->find("status = 1")->toArray();
        
        $this->view->setVar('list',$list);
        
        $this->view->setVar("meta_title","配置管理");
    }
    
    /**
     * 新增配置
     * @author 吴佳恒
     */
    public function addAction(){
        if($this->request->isPost()){
            
            if($this->_model->create($_POST)){
                $this->setDbConfigData(true);
                $this->success('新增成功', $this->url->get(CONTROLLER_NAME));
            }
        } else {
            $this->view->pick("Adminconfig/edit");
        }
    }
    
    /**
     * 编辑配置
     * @author 吴佳恒
     */
    public function editAction($id = 0){
        
        $id = $this->request->get("id","int",0);
        
        if($this->request->isPost()){
            
            
            if($this->_model->save($_POST)){
                $this->setDbConfigData(true);
                $this->success('更新成功', $this->url->get(CONTROLLER_NAME));
            }
        } else {
            $info = $this->_model->getInfo($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }else{
                $info = $info->toArray();
            }
            $this->view->setVar('info', $info);
        }
    }
    
    /**
     * 删除配置
     * @author 吴佳恒
     */
    public function delAction(){
        
        $id = $this->request->get("id");
        
        if(is_array($id)){
            $id = array_unique($id);
            $id = implode(",",$id);
        }

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        
        $list = $this->_model->find("id in ({$id})");
        
        if($list->delete()){
            $this->setDbConfigData(true);
            $this->success('删除成功', $this->url->get(CONTROLLER_NAME));
        }
    }
    
    // 获取某个标签的配置参数
    public function groupAction() {
        $id = $this->request->getQuery("id","int",1);
        //$id     =   I('get.id',1);
        $type = $this->config->CONFIG_GROUP_LIST->toArray();
        //$type   =   C('CONFIG_GROUP_LIST');
        
        $list = $this->_model->find(array(
            "conditions" => "status=1 and [group]={$id}",
            "columns" => 'id,name,title,extra,value,remark,type',
            "order" => "sort"
        ))->toArray();
        
        //$list   =   M(C("BASE_DB_NAME")."."."Config")->where(array('status'=>1,'group'=>$id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->view->setVar('list',$list);
        }
		$this->view->setVar('id',$id);
        $this->view->setVar("meta_title",$type[$id].'设置');
        //$this->meta_title = $type[$id].'设置';
        //$this->display();
    }
    
    
    /**
     * 批量保存配置
     * @author 吴佳恒
     */
    public function saveAction(){
        $config = $this->request->get("config");
        
        
        
        if($config && is_array($config)){
            foreach ($config as $name => $value) {
                if($info = $this->_model->findFirst("name = '{$name}'")){
                    $info->value = $value;
                    $info->save();
                }else{
                    unset($value);
                }
            }
        }
		$this->setDbConfigData(true);
        $this->success('保存成功！');
    }
}