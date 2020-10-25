<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class KvdbController extends AdminBaseController {
    private $Kv=null;

    public function _initialize(){
        parent::_initialize();
        if(!function_exists('memcache_init')){
              header('Content-Type:text/html;charset=utf-8');
              exit('请在SAE平台上运行代码。');
        }
        $this->Kv = new \SaeKV();
    }

    public function index(){
        $list=$this->getAllKv();
        $this->assign('list',$list);
        $this->display();
    }
    private function getAllKv($prefix_key=''){
        $all_key=array();
        $start_key='';
        do{
            $_result=$this->Kv->pkrget($prefix_key,100,$start_key);
            $all_key=array_merge($all_key,array_keys($_result));
            if (count($_result) < 100) break;
            $start_key=key(end($_result));
        }while(true);
        return $all_key;
    }

    public function edit($key){
        if(IS_POST){$this->editPost();exit();}
        $info['key']=$key;
        $info['value']=$this->Kv->get($key);
        $this->assign('info',$info);
        $this->display();
    }
    private function editPost(){
        $data=I('post.','','trim');
        $this->Kv->set($data['key'],$data['value']);
        $this->success('编辑成功');
    }

    public function del($key=''){
        if(!empty($key)){
            $this->Kv->delete($key);
        }else{
            $list=$this->getAllKv();
            foreach ($list as $v) {
                $this->Kv->delete($v);
            }
        }
        $this->success('清理成功');
    }



}