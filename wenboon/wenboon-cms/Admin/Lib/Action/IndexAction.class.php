<?php
/**
 * 后台首页
 **/
class IndexAction extends CommonAction {
    public function index(){
        $this->display();
    }
    public function menu(){
        $this->display();
    }
    public function menu_c(){
        $this->module=M('module')->order('orders asc')->select();
        $this->display();
    }
    public function menu_t(){
        $this->display();
    }
     public function menu_o(){
        $this->display();
    }
    
    public function main(){
        $this->model=M('module')->select();
        $this->qthy=M('member')->count();
        $this->hthy=M('user')->count();
        $this->discuss=M('discuss')->count();
        $this->advertisement=M('advertisement')->count();
        $this->advertext=M('advertext')->count();
        $this->display();
    }
    
    public function head(){
        $this->display();
    }
}
?>