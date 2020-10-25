<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Addons\Advs\Model;
use Think\Model;

/**
 * 分类模型
 */
class AdvsModel extends Model{
	
	/* 自动完成规则 */
	protected $_auto = array(
			array('create_time', 'getCreateTime', self::MODEL_BOTH,'callback'),
			array('end_time', 'getEndTime', self::MODEL_BOTH,'callback'),
	);
	
	
	protected function _after_find(&$result,$options) {
		$sing = M('advertising')->find($result['position']);
		$result['positiontext'] = $sing['title'];
		$result['statustext'] =  $result['status'] == 0 ? '禁用' : '正常';
		$result['create_time'] = date('Y-m-d H:i', $result['create_time']);
		$result['end_time'] = date('Y-m-d H:i', $result['end_time']);
	}
	
	protected function _after_select(&$result,$options){
		foreach($result as &$record){
			$this->_after_find($record,$options);
		}
	}
	
	/*  展示数据  */
	public function AdvsList($param){
		if(isset($param)){
            $map['pos']=$param;
            //TODO 插入广告位标识符判断
			$sing = M('advertising')->where($map)->find();//找到当前调用的广告位
			$where = ' and position = '.$sing['id'];
		}
		if($sing['type'] == 2){
			$advs = $this->where('status = 1 and create_time < '.time().' and end_time > '.time().$where)->order('level asc,id asc')->select();
			foreach($advs as $key=>$val){		
				$data['res'][$key] = $val;
				$cover = M('picture')->find($val['advspic']);
				$data['res'][$key]['path'] = $cover['path'];
			}
			$data['type'] = $sing['type'];
			$data['width'] = $sing['width'];
			$data['height'] = $sing['height'];
            $data['ad']=$sing;
		}else{
			$data = $this->where('status = 1 and create_time < '.time().' and end_time > '.time().$where)->order('level asc,id asc')->find();
			$data['type'] = $sing['type'];
			$data['width'] = $sing['width'];
			$data['height'] = $sing['height'];

		}

		return $data;
	}
	
	/* 获取编辑数据 */
	public function detail($id){
		$data = $this->find($id);
		$cover = M('picture')->find($data['advspic']);
		$sing = M('advertising')->find($data['position']);
		$data['path'] = $cover['path'];
		$data['type'] = $sing['type'];
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
		/* 获取数据对象 */
		$data = $this->create();
		
		
		
		$sing = M('advertising')->find($data['position']);
		
		//广告位的禁用判断
		if($sing['status'] == 0){
			$this->error = '这是个禁用的广告位！';
			return false;
		}
		
		if(empty($data)){
			return false;
		}
		if($data['create_time']>=$data['end_time']){
			$this->error = '开始时间必须小于结束时间！';
					return false;
			
		}
		/* 添加或新增基础内容 */
		if(empty($data['id'])){ //新增数据
			//单一广告判断
			if($sing['type'] != 2){//判断单图
				$count = $this->where('position = '.$data['position'])->count();
				if($count > 0){
					$this->error = '单图广告、文字广告和代码广告位只允许添加一条广告信息！';
					return false;
				}
			}	
            
			
			
			$id = $this->add(); //添加基础内容
			if(!$id){
				$this->error = '新增广告内容出错！';
				return false;
			}
		} else { //更新数据
			
			
			
			$status = $this->save(); //更新基础内容
			if(false === $status){
				$this->error = '更新广告内容出错！';
				return false;
			}
		}
	
		//内容添加或更新完成
		return $data;
	
	}	
	
	/* 时间处理规则 */
	protected function getCreateTime(){
		$create_time    =   I('post.create_time');
		return $create_time?strtotime($create_time):NOW_TIME;
	}
	
	/* 时间处理规则 */
	protected function getEndTime(){
		$end_time    =   I('post.end_time');
		return $end_time?strtotime($end_time):NOW_TIME;
	}	
	
}