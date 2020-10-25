<?php 
namespace Admin\Controller;
use Think\Controller;
	class CatController extends CommonController 
	{
		//编辑分类
		public function index(){

			$data = D('cat')->field('id,concat(path,"-",id) abspath,name')->order('abspath,id')->select();
			
			$this->assign("select",$data);

			$this->display();
		}
		public function add(){
			$this->assign('select',D('cat')->selectform());
			$this->display();
		}
		public function mod(){
			$catobj = D("cat");
			$cat = $catobj->find($_GET['id']);
			$this->assign('select',$catobj->selectform('pid',$cat['pid'],$cat['id']));
			$this->assign('cats',$cat);
			$this->display();
		}
		public function insert(){
			$cat = D('cat');
			
			if($_POST['pid']=='0'){
				$_POST['path']='0';
			}
			else{
				$cats=$cat->find($_POST['pid']);

				$_POST['path'] = $cats['path'].'-'.$_POST['pid'];

			}
			if($cat->add($_POST)){
				$this->success('添加分类成功',__CONTROLLER__.'/index');
			}else{
				$this->error('添加分类失败',__CONTROLLER__.'/add');
			}
		}
		public function update(){
			$cat = D("cat");

			if($cat->upd()){
				$this->success('修改成功',__CONTROLLER__.'/index');
			}else{
				$this->error('修改失败',__CONTROLLER__.'/mod/id/$_POST["id"]');
			}
		}
		public function delete(){
			$cat = D("cat");

			if($cat->del()){
				$this->success('删除成功',__CONTROLLER__.'/index');
			}else{
				$this->error($cat->getMsg(),__CONTROLLER__.'/index');
			}
		}

	}
 ?>