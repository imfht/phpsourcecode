<?php

namespace Db\Model;
use Think\Model;

/**
 * 创建DBCell 表列数据
 */

class DbcellModel extends Model{
	
	/**
	 * 数据表前缀
	 * @var string
	 */
	protected $tablePrefix = "s_";

	/* Dbcell模型自动完成 */
	protected $_auto = array(
		array('cpk', '1'),
		array('tofield','1'),
		array('cnotnull','1'),
		array('cflag','1'),
		array('caddflag','1'),
		array('toquery','1'),
		array('toassign','1')
	);

	/**
	 *创建Dbcell视图
	 *@param $tid 'tid'
	 *@param $cell[0] 'fname'
	 *@param $cell[1] 'fnamec'
	 *@param $cell[2] 'flx'
	 *@param $cell[3] 'fcd'
	 *@param $cell[4] 'todic'
	 *@param $cell[5] 'indexorder'
	 *@param $cell[6] 'ctname'
	 *@param $cell[7] 'sharecell'
	 *@return integer 最新试图ID
	 */
	public function InsertDbcell($tid,$cellArray){
        $i=0;
        foreach ($cellArray as $key => $value) {
			$data = array(
				'tid' => $tid,
				'fname' => $value['tname'],
				'fnamec' => $value['cname'],
				'flx' => $value['ttype'],
				'fcd' => $value['tcd'],
				'todic' => '',
				'indexorder' => $i++,
				'ctname' => $value['tname'],
				'sharecell' => '',
				'cpk'=>1,
				'tofield'=>1,
				'cflag'=>1,
				'caddflag'=>1,
				'toquery'=>1,
				'toassign'=>1,
			);
			/* 添加Dbcell */
			if($this->create($data)){
				$result =+ $this->add();
			}
		}
		return $result ? $result : 0;;
	}


	/**
	 * 通过视图ID获取数据
	 * @param $tid 'tid'
	 */
	public function GetListForTid($tid){

		$map['tid'] = $tid;

		$list = $this->where($map)->order('indexorder')->select(); 

		return $list;
	}

	/**
	 * 通过tID删除数据
	 * @param $tid 表ID
	 */
	public function DeleteDbCell($tid){
		return $this->where('tid='.$tid)->delete();
	}
}