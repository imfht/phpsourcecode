<?php

namespace Db\Model;
use Think\Model;

/**
 * 创建Dict 表列数据
 */

class DictModel extends Model{
	
	/**
	 * 数据表前缀
	 * @var string
	 */
	protected $tablePrefix = "y_";

	/* Dbcell模型自动完成 */
	protected $_auto = array(

	);

	/**
	 * 通过视图ID获取数据
	 */
	public function GetDict(){
		if($this){
			return $list = $this->order('id desc')->select(); 
		}
	}
}