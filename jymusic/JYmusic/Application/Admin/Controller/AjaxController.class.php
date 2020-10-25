<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class AjaxController extends AdminController {
    public function index(){
		$this->show('非法操作');
	}
	
	public function findData(){
		 if (IS_AJAX){
		 	//sleep(3);
		 	$table = I('post.table');
		 	$sort = I('post.sort');	
		 	if ($sort){
		 		$map['sort'] = $sort;
		 	}else{
		 		$map['sort'] = '0';
		 	} 	
		 	if($table && $map){
		 		$data = M($table)->field('id,name')->where($map)->select();
				$this->ajaxReturn($data);
			}
		 }else{
		 	$this->error('非法请求');
		 }
		
	}
	
	public function editData(){
		 if (IS_AJAX){
		 	//sleep(5);
		 	$table = $_POST['table'];
		 	$map['sort'] = $_POST['sort'];		 	
		 	if($table && $map['sort']){
		 		$data = M($table)->field('id,name')->where($map)->select();
				$this->ajaxReturn($data);
			}
		 }else{
		 	$this->error('非法请求');
		 }
		
	}
}