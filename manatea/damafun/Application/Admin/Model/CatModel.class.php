<?php
namespace Admin\Model;
use Think\Model;
	class CatModel extends Model{

		public function upd(){
			$data1 = $this->field('path')->find($_POST['pid']);

			$newpath = $data1['path']; //0

			$data2 = $this->field('path')->find($_POST['id']);

			$xpath = $data2['path']; //0-1

			if($_POST['pid']==0){
				$_POST['path']=0; //0
			}else{
				$_POST['path']= $newpath.'-'.$_POST['pid']; //0-10
			}

			$srcpath = $xpath.'-'.$_POST['id']; // 0-1-3
		//	$this->where("path like '{$srcpath}%'")->save(array('path'=>"replace(path,{$xpath},{$_POST['path']})"))&&$this->where("id = {$_POST['id']}")->save($_POST)){	
			if($this->execute("UPDATE __CAT__ SET path = replace(path,'{$xpath}','{$_POST['path']}') WHERE path like '{$srcpath}%'")&&$this->where("id = {$_POST['id']}")->save($_POST)){
						return true;
			}else{
				if($this->where("id = {$_POST['id']}")->save($_POST))
					return true;
				return false;
			}
		}

		//无限分类 select
		//参数1：数据库中父类列名
		//参数1：视频所属类别的ID
		//参数3：视频的id
		public function selectform($name= "pid",$pid='0',$id='0'){
			$html = '';
			
			$data = $this->field("id,concat(path,'-',id) as abspath,name")->order('abspath,id')->select();



			$html .='<select class="form-control" name = '.$name.'>';

			$html .='<option value = 0>|--根目录--|</option>';
			foreach ($data as $row) {

				$num = count(explode('-', $row['abspath']));

				$space = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $num-2);

				$selected = $pid == $row['id'] ? 'selected' : '';

				if($selected=='selected'||$pid==0||!in_array($id,explode('-',$row['abspath']))){
					$html .="<option {$selected} value={$row['id']}>|{$space}|-{$row['name']}</option>";
				}
				

			}

			$html .='</select>';
			
			return $html;
		}
		public function del(){

			$cat = $this->field('path')->find($_GET['id']);

			$srcpath = $cat['path'].'-'.$_GET['id'];

			if($this->where(array('pid'=>$_GET['id']))->count() > 0){
				$this->setMsg("当前分类下有子分类，不能删除");
				return false;
			}
			if(D('video')->where(array('pid'=>$_GET['id']))->count() > 0){
				$this->setMsg("当前分类下有视频，不能删除");
				return false;
			}
			if($this->where('id = '.$_GET['id'])->delete()){
				return true;
			}
			return false;

		}
	}	
?>