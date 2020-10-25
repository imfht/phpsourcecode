<?php

namespace Db\Model;
use Think\Model;

/**
 * DBForm基础表模型
 */
class DbformModel extends Model{

	/**
	 * 数据表前缀
	 * @var string
	 */
	protected $tablePrefix = "s_";



	/* DBForm模型自动完成 */
	protected $_auto = array(
		array('form_flag', 1),
		array('form_css',1),
		array('form_createtime',NOW_TIME),
		array('form_version',1)
	);

	/**
	 *创建DBForm视图
	 *@param $dbForm[0] 'tid'
	 *@param $dbForm[1] 'vtype'
	 *@param $dbForm[2] 'vname'
	 *@param $dbForm[0] 'form_table'
	 *@return integer 最新试图ID
	 */
	public function InsertDbForm($dbForm){
		$data = array(
			'tid' => $dbForm[0],
			'vtype' => $dbForm[1],
			'vname' => $dbForm[2],
			'form_table'  => $dbForm[3],
		);

		/* 添加DBForm */
		if($this->create($data)){
			$vid = $this->add();
			return $vid ? $vid : 0; //0-未知错误，大于0-成功
		} else {
			return $this->getError(); //错误详情见自动验证注释
		}
	}

	public function UpdateDbForm($dbForm,$vid){
		$data = array(
			'tid' => $dbForm[0],
			'vtype' => $dbForm[1],
			'vname' => $dbForm[2],
			'form_table'  => $dbForm[3],
		);

		/* 添加DBForm */
		if($this->create($data)){
			$vid = $this->where('vid='.$vid)->save();
			return $vid ? $vid : 0; //0-未知错误，大于0-成功
		} else {
			return $this->getError(); //错误详情见自动验证注释
		}
	}

	/**
	 *更新DBForm视图
	 *@param $dbForm 试图内容
	 *@param $tid    表id
	 *@return $list  对应视图
	 */
	public function UpdateDbFormForTid($dbForm,$vid){
		$data = array(
			'vname' => $dbForm[0]
		);

		/* 添加DBForm */
		if($this->create($data)){
			$vid = $this->where('vid='.$vid)->save();
			return $vid ? $vid : 0; //0-未知错误，大于0-成功
		} else {
			return $this->getError(); //错误详情见自动验证注释
		}
	}

	/**
	 *查询DBForm视图
	 *@param $tid 'tid'
	 *@return $list 对应视图
	 */
	public function GetDBFormList($tid){

		$map['tid'] = $tid;

		$list = $this->where($map)->select(); 

		return $list;
	}


	/**
	 * 通过vid获取视图数据
	 */
	public function GetDBFormVid($vid){
		
		$list = $this->where('vid='.$vid)->find(); 

		return $list;
	}

	/**
	 * 获取所有共享字段
	 */
	public function GetShare(){
		
		$list = $this->where("vtype='share'")->select();
		return $list;
	}

	/**
	 * 删除视图
	 */
	public function del($vid){
		
		$map['vid'] = $vid;
		return $this->where($map)->delete();
	}
}
