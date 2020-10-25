<?php 
namespace Addons\Diyform\Controller;
use Think\Controller;
class DiyformController extends Controller{
	//显示表单
	public function showform(){
		$tem = $this->getTemplates();
		$list = $this->getFormTitle($tem['id']);
		$builder = new \OT\Builder('config');
		$builder->title($tem['title']);
		foreach($list as $key => $value){
			if(!empty($value['textextra'])){
				$textextra = explode('|',$value['textextra']);
				foreach ($textextra as $k => $v) {
					$new = explode(',',$v);
					foreach($new as $o => $l){
						$array[$k] = $l;
					}
				}
			}
			$builder->key($value['textname'],$value['texttitle'],'',$value['texttype'],$array);
		}
        $builder->buttonSubmit(U('Home/addons/execute/_addons/Diyform/_controller/Diyform/_action/addData/table/'.I('get.id')))
		        ->data($list)
		        ->display();
	}

	//发表数据
	public function addData(){
		$map['id'] = I('get.table');
		$model = D('Addons://Diyform/Diyform');
		$result = $model->where($map)->find();
		$table = D($result['table']);
		if($table->create()){
			$table->add();
			$this->success('发布成功！',U('home/addons/execute/_addons/Diyform/_controller/Diyform/_action/listData/table/'.$map['id']));
		}else{
			$this->error($table->getError());
		}
	}

	//展示页面
	public function listData(){
		$map['id'] = I('get.table');
		$model = D('Addons://Diyform/Diyform');
		$result = $model->where($map)->find();
		$tabledata = D($result['table'])->select();
		$title = D('Addons://Diyform/FormInfo');
		$titledata = $title->where('tableid='.$result['id'])->field('textname,texttitle')->select();
		$listdata = array_merge($tabledata,$titledata);
		foreach ($tabledata as $key => $value) {
			foreach ($titledata as $k => $v) {
				$name = $value;
				if($value['id'] == $value['id']){
					unset($value);
				}
				$list[] = array_merge($name,$v);
			}
		}
		$list = array_filter($list);
		$builder = new \OT\Builder();
		$builder->title('数据列表');
		foreach ($titledata as $key => $value) {
			$builder->keyText($value['textname'],$value['texttitle']);
		}
        $builder->setStatusUrl(U('setRuleStatus'))
            ->data($list)
            ->display();
	}

	//获取模板地址
	public function getTemplates(){
		$map['id'] = I('get.id');
		$model = D('Addons://Diyform/Diyform');
		return $model->where($map)->find();
	}

	//获取表单title
	public function getFormTitle($id){
		$map['tableid'] = $id;
		$model = D('FormInfo');
		//查找数据
		$result = $model->where($map)->field()->select();
		return $result;
	}
}