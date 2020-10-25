<?php
namespace Admin\Model;
use Think\Model;

class GuestbookModel extends Model{

	//内容过滤/填充(完成字段1,完成规则,[完成条件,附加规则,函数参数])
	//完成条件(1:新增时候处理,2:修改时候处理,3:全部时候处理|默认)
	protected $_auto = array (
		array('reply_time','date',2,'function',array('Y-m-d H:i:s')),
        array('reply_uid',UID,2),
	);

}