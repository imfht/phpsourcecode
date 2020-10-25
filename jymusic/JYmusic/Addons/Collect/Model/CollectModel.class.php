<?php

namespace Addons\Collect\Model;
use Think\Model;

/**
 * ColletRule模型
 */
class CollectModel extends Model{
	protected $tableName = 'collect_rule';
	protected $_validate = array(
        array('rule_name', 'require', '规则名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
		array('page_rule', 'require', '分页规则不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
		array('link_wrap_rule', 'require', '链接外部规则不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
		array('link_rule', 'require', '链接规则不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
		array('title_rule', 'require', '音乐名称规则不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH), 
		array('play_rule', 'require', '播放地址规则不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
    );
	
	/* 自动完成规则 */
	protected $_auto = array(
			array('create_time',NOW_TIME, self::MODEL_BOTH),
	);
	
	
	protected function _after_find(&$result,$options) {
		$result['statustext'] =  $result['status'] == 0 ? '禁用' : '正常';
		$result['create_time'] = date('Y-m-d', $result['create_time']);
	}
	
	protected function _after_select(&$result,$options){
		foreach($result as &$record){
			$this->_after_find($record,$options);
		}
	}
	
	/*  展示数据  */
	public function SliderList(){		
		$data = $this->where('status = 1')->cache(true,86400)->order('level desc,id asc')->select();
		foreach($data as $key=>$val){
			
			if ($val['cover_id']){
				$cover = M('picture')->find($val['cover_id']);
				$data[$key]['path'] = __ROOT__.$cover['path'];
			}else{
				$data[$key]['path'] =$data[$key]['img_url'];
			}
		}
		return $data;
	}
	
	/* 获取编辑数据 */
	public function detail($id){
		$data = $this->find($id);
		return $data;
	}
	
	/* 禁用 */
	public function forbidden($id){
		return $this->save(array('id'=>$id,'status'=>'0'));
	}
	
	/* 启用 */
	public function off($id){
		return $this->save(array('id'=>$id,'status'=>'1'));
	}
	
	/* 删除 */
	public function del($id){
		return $this->delete($id);
	}
	
	/**
	 * 新增或更新一个文档
	 * @return boolean fasle 失败 ， int  成功 返回完整的数据
	 */
	public function update(){
		C('DEFAULT_FILTER','');
		/* 获取数据对象 */
		$data = $this->create();
		if(empty($data)){
			return false;
		}
		/* 添加或新增基础内容 */
		if(empty($data['id'])){ //新增数据
			$id = $this->add(); //添加基础内容
			if(!$id){
				$this->error = '新增内容出错！';
				return false;
			}
		} else { //更新数据
			$status = $this->save(); //更新基础内容
			
			if(false === $status){
				$this->error = '更新内容出错！';
				return false;
			}
		}
	
		//内容添加或更新完成
		return $data;
	
	}	
	
}
