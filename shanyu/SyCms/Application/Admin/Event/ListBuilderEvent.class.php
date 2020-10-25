<?php
namespace Admin\Event;
use Think\Controller;

class ListBuilderEvent extends Controller{
	public $_action='';
	public $_search='';
	public $_batch='';
	public $_field=array();
	public $_field_action=array();
	public $_data_list=array();

	public function addAction($title,$name,$class){
		$action='';
		if(strpos($class,'Ajax') === false){
			$action.='<a class="btn '.$class.'" href="'.U($name).'">'.$title.'</a>';
		}else{
			$action.='<a class="btn '.$class.'" data-url="'.U($name).'" href="javascript:;">'.$title.'</a>';
		}
		$this->_action.=$action;
		return $this;
	}
	public function addBatch($title,$name){
		$batch='<option value="'.$name.'">'.$title.'</option>';
		$this->_batch.=$batch;
		return $this;
	}
	public function addSearch($title,$name,$way='',$type='',$extra=''){
		if(!empty($way) && $way != "eq"){
			$field=$name.'['.$way.']';
		}else{
			$field=$name;
		}

		$search='';
		if(empty($type) || $type=="text"){
			$search.='<span class="text-icon">'.$title.'</span><input name="'.$field.'" type="text">';
		}elseif($type=="select"){
			if(is_string($extra)) $values=str_arr($extra);
			else $values=$extra;
			$search.='<span class="text-icon">'.$title.'</span><select name="'.$field.'">';
			foreach ($values as $k => $v) {
				$search.='<option value="'.$k.'">'.$v.'</option>';
			}
			$search.='</select>';
		}


		$this->_search.=$search;
		return $this;
	}
	public function addField($title,$name,$type='',$extra=''){
            if($type == 'batch') $title='<input class="CheckAll" type="checkbox">';

            $this->_field[] = array('title' => $title, 'name' => $name, 'type' => $type, 'extra' => $extra);
            return $this;
	}
	public function addFieldAction($title,$name,$class){
        $this->_field_action[] = array('title' => $title, 'action' => $name, 'class' => $class);
        return $this;	
	}
    public function dataList($data_list){
        $this->_data_list = $data_list;
        return $this;
    }

    public function display($tpl='Common/builder-list-base'){

    	$this->assign('_action', $this->_action);
    	$this->assign('_search', $this->_search);
    	$this->assign('_batch', $this->_batch);

   		$this->assign('_field', $this->_field);
    	$_list=$this->formatList();
    	$this->assign('_list', $_list);

    	parent::display($tpl);

    }

    private function formatList(){
    	$list=$this->_data_list;
    	$field=$this->_field;
    	$action=$this->_field_action;

		foreach ($list as &$v) {
			//添加操作按钮
			if(!empty($action)){
				$v['field_action']='';
				foreach ($action as $a) {
					if(strpos($a['class'],'Ajax') === false){
						$v['field_action'].='<a class="btn '.$a['class'].'" href="'.U($a['action'],array('id'=>$v['id'])).'">'.$a['title'].'</a>';
					}else{
						$v['field_action'].='<a class="btn '.$a['class'].'" data-url="'.U($a['action'],array('id'=>$v['id'])).'" href="javascript:;">'.$a['title'].'</a>';
					}
				}
			}

			//根据标头字段类型编译数据
			foreach ($field as $f) {
				if(empty($f['type'])) continue;
				switch ($f['type']) {
					case 'time':
						$v[$f['name']]=date('Y-m-d H:i:s',$v[$f['name']]);
						break;
					case 'date':
						$v[$f['name']]=date('Y-m-d',$v[$f['name']]);
						break;
					case 'status':
						if($v[$f['name']]){
							$v[$f['name']]='<i class="icon-true"></i>';
						}else{
							$v[$f['name']]='<i class="icon-false"></i>';
						}
						break;
					case 'image':
						$v[$f['name']]='<a class="AjaxImage" data-url="'.__ROOT__.$v[$f['name']].'" href="javascript:;"><img src="'.__ROOT__.$v[$f['name']].'" width="100" height="30" /></a>';
						break;
					case 'city':
						$citys=explode(',', $f['name']);
						$v[$f['name']]='';
						$city_name=D('Common/City')->getSelect();
						foreach ($citys as $city) {
							$v[$f['name']] .= $city_name[$v[$city]].' ';
						}
						break;
					case 'cn':
						if(is_string($f['extra'])){
							$v[$f['name']]=int_str($v[$f['name']],$f['extra']);
						}elseif(is_array($f['extra'])){
							$v[$f['name']]=$f['extra'][$v[$f['name']]];
						}
						break;
					case 'sort':
						$v[$f['name']]='<input type="text" name="'.$f['name'].'['.$v['id'].']" value="'.$v[$f['name']].'" size="1" class="tc">';
						break;
					case 'batch':
						$v[$f['name']]='<input class="CheckOne" type="checkbox" name="pk[]" value="'.$v['id'].'">';
						break;
				}
			}

		}
		return $list;
    }



}