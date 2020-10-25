<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class  EmailController extends AdminController {
    public function index(){
		$config = get_custom_config('email_config');
		
		$this->assign('config',$config); 	
        $this->meta_title = '邮件设置';
       	$this->display('Config/email');
	}
	
    //
    public function mod(){
		if(IS_POST){
			$config  = I('post.');	
			$text ="<?php\treturn " . var_export($config, true).';';		
			$file = D('File')->writeConfig('email_config',$text);
			if($file){
				$this->success('配置保存成功！');
			}else{
				$this->error('配置保存失败,请检查[Application/Common/Conf]文件夹是否有可写权限！');	
			}
		}else{			
			$this->error('参数错误');				
		}
    }
    
    public function test(){
    	$data=I('post.');
    	if(empty($data['sendto_email']))$this->error('请填写发送邮箱');
    	$regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[-_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i';
		if (!preg_match($regex, $data['sendto_email']))$this->error('电子邮件格式不正确');
    	$email = D('Mail')->test_email($data);
    	if ($email){
    		$this->success('测试邮件发送成功！');
    	}else{
    		$this->error('测试邮件发送失败！');
    	}
    }
}