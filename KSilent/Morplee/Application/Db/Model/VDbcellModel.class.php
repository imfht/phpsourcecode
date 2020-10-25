<?php

namespace Db\Model;
use Think\Model;

/**
 * VDbcell 表列数据
 */

class VDbcellModel extends Model{
	
	/**
	 * 数据表前缀
	 * @var string
	 */
	protected $tablePrefix = "v_";

	protected $tableName="dbcell";



	/* Dbcell模型自动完成 */
	protected $_auto = array(
		array('jumpto', ''),
		array('controltype',''),
		array('inpname',''),
		array('splittitle',''),
		array('colspan',''),
	);

	/**
	 *创建Dbcell视图
	 *@param $vid 'vid'
	 *@param $tid 'tid'
	 *@return integer 最新试图ID
	 */
	public function InsertVDbcell($vid,$tid,$cellArray){
        $i=0;
        foreach ($cellArray as $key => $value) {
			$data = array(
				'vid' => $vid,
				'vtype' =>  empty($value['vtype']) ? 'list' : $value['vtype'] ,
				'vname' => $value['tname'],
				'tid' => $tid,
				'fname' => $value['tname'],
				'fnamec' => $value['cname'],
				'indexorder' => empty($value['indexorder']) ? $i++ : $value['indexorder'],
				'areaname' => '',
				'isquery' => '1',
				'ispk' => $value['ispk']=='on' ? '1' : '0',
				'showcontrol' => '',
				'isreadonly' => $value['isreadonly']=='on' ? '1' : '0',
				'isnotnull' => $value['isnotnull']=='on' ? '1' : '0',
				'shareview' => empty($value['shareview']) ? '0' : $value['shareview'],
				'colspan' => '',
				'vdic' => empty($value['vdic']) ? '0' : $value['vdic'],
			);

			/* 添加Dbcell */
			if($this->create($data)){
				$result += $this->add();
			}
		}

		return $result ? $result : 0;;

	}

	public function UpdateVDbcell($vid,$tid,$vname,$cellArray){
        $i=0;
		$data = array(
			'vtype' =>  empty($cellArray['vtype']) ? 'list' : $cellArray['vtype'] ,
			'vname' => $cellArray['tname'],
			'fname' => $cellArray['tname'],
			'fnamec' => $cellArray['cname'],
			'indexorder' => empty($cellArray['indexorder']) ? $i++ : $cellArray['indexorder'],
			'areaname' => '',
			'isquery' => '1',
			'ispk' => $cellArray['ispk']=='on' ? '1' : '0',
			'showcontrol' => '',
			'isreadonly' => $cellArray['isreadonly']=='on' ? '1' : '0',
			'isnotnull' => $cellArray['isnotnull']=='on' ? '1' : '0',
			'shareview' => empty($cellArray['shareview']) ? '0' : $cellArray['shareview'],
			'colspan' => '',
			'vdic' => empty($cellArray['vdic']) ? $i++ : $cellArray['vdic'],
		);

		/* 添加Dbcell */
		if($this->create($data)){
			$tmp=array(
				'vid' => $vid,
				'tid' => $tid,
				'vname' => $vname,
			);
			$result += $this->where($tmp)->save();
		}

		return $result ? $result : 0;;

	}
	/**
	 * 通过视图ID获取数据
	 * @param $vid 'vid'
	 */
	public function GetListForVid($vid){

		$map['vid'] = $vid;

		$list = $this->where($map)->order('indexorder')->select(); 

		return $list;
	}

	/**
	 * 删除数据
	 * @param $vid 'vid'
	 */
	public function del($vid){
		$map['vid'] = $vid;
		return $this->where($map)->delete();
	}

	/**
	 * 通过tID删除数据
	 * @param $tid 表ID
	 */
	public function DeleteVDbCell($tid){
		return $this->where('tid='.$tid)->delete();
	}
}