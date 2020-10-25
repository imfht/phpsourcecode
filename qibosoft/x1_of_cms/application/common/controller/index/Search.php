<?php
namespace app\common\controller\index;

use app\common\controller\IndexBase;

//搜索页
class Search extends IndexBase
{
    
    protected $model;   //内容
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'content');
    }
    
	public function index($mid=0){
	    if(empty($mid) || empty(model_config($mid))){
	        $mid = current(model_config())['id'];  //如果没指定MID或者MID有误的话，就取默认第一个mid
	    }
	    
	    $f_array = get_field($mid);    //某个模型的所有自定义字段
	    $listdb = null;                //设置为null是方便模板那里做判断，是否查询过数据库
	    if (input('keyword')){
	        $data = input();
	        $data['type'] || $data['type']='title';
	        $type = $data['type'];
	        $keyword = $data['keyword'];
	        if($type && $keyword && !in_array($type,['uid']) && empty($f_array[$type])){
	            $this->error('搜索类型不存在！');
	        }
	        $map = $this->get_map($data,$f_array);
	        $listdb = $this->model->getListByMid($mid,$map,'id desc',20,[],true);
	        $pages = $listdb->render();
	        $listdb = getArray($listdb)['data']?:[];   //避免出现null与上面的冲突
	    }
	    $this->assign('listdb',$listdb);
	    $this->assign('pages',$pages);
	    $this->assign('mid',$mid);
	    $this->assign('f_array',$f_array);
	    return $this->fetch();
	}
	
	protected function get_map($data=[],$f_array=[]){
	    $map = [];
	    
	    //查找哪个字段的关键字
	    if($data['type'] && $data['keyword'] && $f_array[$data['type']]){
	        if ($data['ruletype']==3){
	            $map[$data['type']] = $data['keyword'];
	        }elseif($data['ruletype']==2){
	            $map[$data['type']] = ['LIKE',"{$data['keyword']}%"];
	        }elseif($data['ruletype']==1){
	            $map[$data['type']] = ['LIKE',"%{$data['keyword']}"];
	        }else{
	            $map[$data['type']] = ['LIKE',"%{$data['keyword']}%"];
	        }
	    }elseif($data['type']=='uid' && $data['keyword']){ //按用户UID搜索
	        $map['uid'] = $data['keyword'];
	    }
	    
	    //单选项或者是下拉框
	    foreach ($data AS $key=>$value){
	        $rs = $f_array[$key];
	        if (empty($rs)){
	            continue;
	        }
	        if($value!=='' && $rs['ifsearch'] && in_array($rs['type'],['radio','select']) ){
	            $map[$rs['name']] = $value;
	        }
	    }
	    
	    //多选项
	    foreach ($data AS $key=>$value){
	        $rs = $f_array[$key];
	        if (empty($rs)){
	            continue;
	        }
	        if( is_array($value) && $rs['ifsearch'] && in_array($rs['type'],['checkbox']) ){
	            foreach($value AS $_k=>$_v){
	                $value[$_k] = "%,{$_v},%";
	            }
	            $map[$rs['name']] = ['LIKE',$value];
	        }
	    }
	    
	    //所属栏目
	    if($data['fid']){
	        $map['fid'] = $data['fid'];
	    }
	    return $map;
	}

}
