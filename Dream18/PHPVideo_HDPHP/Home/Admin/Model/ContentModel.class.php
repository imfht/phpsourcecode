<?php
/**
 * 视频管理关联模型
 * 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class ContentModel extends RelationModel
{
	// 数据主表
	public $table = 'content';

	// 私有对象
	private $aid,$cid;

	// 构造函数
	public function __init()
	{
		$this->aid = Q('aid', 0, 'intval');
		$this->cid = Q('cid', 0, 'intval');
	}

	// 自动完成
	public $auto = array(
		// 添加视频自动完成时间
		array('addtime','time','function', 2, 1),
		// 更新视频自动完成时间
		array('updatetime','time','function', 2, 3),
	);

	// 关联数据
	public $relation = array(
		'cate'		=> array(// 关联表
		'type'		=> HAS_ONE, // 包含一条主表记录
		'foreign_key'	=> 'cid', //user_info 表字段
		'parent_key'	=> 'cid', //user 表字段
		'field'			=> array('catname' => '_cate'), // 关联表检索的字段
		),
		'user'		=> array(// 关联表
		'type'		=> HAS_ONE, // 包含一条主表记录
		'foreign_key'	=> 'uid', //user_info 表字段
		'parent_key'	=> 'uid', //user 表字段
		'field'			=> array('username' => '_author'), // 关联表检索的字段
		),
	);

	/**
	 * [createAdd 添加视频]
	 * @return [type] [description]
	 */
	public function CreateAdd()
	{
		if($this->create())
		{
			if($this->Usernick())
			{
				return $this->add();
			}
		}
	}


	/**
	 * [createedit 修改视频]
	 * @return [type] [description]
	 */
	public function CreateEdit()
	{
		if($this->create())
		{
			if($this->Usernick())
			{
				return $this->where(array('aid'=> $this->aid))->save();
			}
		}
	}


	/*--------------------------------------------------属性定义-------------------------------------------------------*/


	// 发布者账户名转id
	public function Usernick()
	{
		// 获取会员ID
		$result = M('user')->where(array('username'=> $this->data['uid']))->find();
		// 重组数组
		$this->data['uid'] = $result['uid'];
		return $this->data;
	}



	/**
	 * [Delvideo 删除七牛云储存视频]
	 */
	public function Delvideo()
	{
	}
}