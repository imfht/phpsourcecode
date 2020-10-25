<?php 
/**
	 * CourseSEL  前台
	 * @Author hxb0810(halexcode)
	 * Email: hxb0810@163.com
	 * Tel:   15534378771
	 * Date:  2017-10-07 21:00
	 * @Tool Sublime
	 * 
	 */
namespace Admin\Controller;
use Think\Controller;
class ComController extends Controller {
	public function _initialize(){
                    //初始化的时候检查用户session是否过期
                	if(!session('?username')|| session('username')=='') {
                		$this->error('您尚未登录或者登录已经过期，请重新登录',U('Index/Login/index'));
                    		//$this->redirect('Login/index'); 
            		}
        	}
}
?>
