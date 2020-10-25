<?php
namespace TPHelper\Controller;
use Think\Controller;
class CommonController extends Controller {
	public $admin_account,$admin_pw;
	public $ap,$apInfo;
    public $md, $ac, $con;
    public function _initialize()
    {
    	$this->ac = ACTION_NAME;
        $this->con = CONTROLLER_NAME;
        $this->md = MODULE_NAME;
    	$this->admin_account = session('admin_account');
    	$this->admin_pw = session('admin_pw');
    	if ($this->admin_account != C('admin_account')) {
    		redirect(U('Public/login'));
    	}
    	if ($this->con != 'Index') {
    		$this->ap = I('ap');
            if (!$this->ap) $this->ap = I('get.ap');
            if (!$this->ap) $this->ap = I('post.ap');
            if (!$this->ap) {
                $this->error('请选择应用',U('Index/index'));
            }
            $this->apInfo = F($this->ap.'_index');

            if ($this->apInfo) {
                foreach ($this->apInfo as $key => $value) {
                    C($key,$value);
                }
            }
        	$this->assign('ap',$this->ap);
    	}
    }
}