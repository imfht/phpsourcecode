<?php
namespace Home\Model;
use Think\Model;
class CatModel extends Model{

	
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


			//找出包含该分类下所有子类ID
			//$cid 当前分类ID
		public	function getChildCatId($cid)
			{
					$cat = $this->field("id,name,path")->select();
					$arr = array();

					foreach ($cat as $key) {
						if(in_array($cid, explode('-',$key['path']))){
							array_push($arr,$key['id']);
						}
					}

					//若该分类为叶子分类，则将当前分类加入
					if(empty($arr)){
						array_push($arr, $cid);
					}
					return $arr;
			}

			//找出该分类下的所有子类(id,name.path)
			//$cid 当前分类ID
		public	function getChildCat($cid){

				$cat = $this->field("id,name,path")->select();
				$arr = array();

				foreach ($cat as $key) {
					if(in_array($cid, explode('-',$key['path']))){
						array_push($arr,$key);
					}
				}

				return $arr;
			}
	}	
?>