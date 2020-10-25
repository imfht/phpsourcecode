<?php
namespace Admin\Event;
use Think\Controller;

class FormBuilderEvent extends Controller{
    public $_tit='';
	public $_input=array();
	public $_data_info=array();

    public function addTit($title, $name, $class=''){
        $tit='';
        $tit.='<a class="'.$class.'" href="'.U($name).'">'.$title.'</a>';
        $this->_tit.=$tit;
        return $this;
    }

	public function addInput($title, $name, $type, $vaild='', $tip='',$param='',$attr=''){
        $input=array();
        $input['title'] = $title;
        $input['name'] = $name;
        $input['type'] = $type;
        $input['valid'] = $vaild;
        $input['tip'] = $tip;
        if(!empty($param) && is_string($param)) $param=str_arr($param);
        $input['param'] = $param;
        $input['attr'] = $attr;
        $this->_input[] = $input;
        return $this;
	}

	public function dataInfo($data_info){
        $this->_data_info = $data_info;
        return $this;
	}

    public function display($tpl=''){
    	$input=$this->formatInfo();
        $tit=$this->_tit;

    	$this->assign('_input', $input);
        $this->assign('_tit', $tit);

        if(empty($tpl)){
            $tpl='Common/builder-form-ajax';
        }else{
            $tpl='Common/builder-form-'.$tpl;
        }
    	

    	parent::display($tpl);
    }

    private function formatInfo(){
    	$input=$this->_input;
    	$info=$this->_data_info;
    	if(!$info) return $input;

		foreach ($input as &$v) {

			if(!empty($v['name']) && isset($info[$v['name']])){ 
				$v['value']=$info[$v['name']]; 
			}else{
                switch ($v['type']) {
                    case 'select':
                    case 'radio':
                        $v['value']=0;
                        break;
                    case 'city':
                        if(isset($info['province'])) $v['value']['province']=$info['province'];
                        if(isset($info['city'])) $v['value']['city']=$info['city'];
                        if(isset($info['area'])) $v['value']['area']=$info['area'];
                        break;
                    default:
                        $v['value']='';
                        break;
                }
			}
		}

		return $input;
    }



}