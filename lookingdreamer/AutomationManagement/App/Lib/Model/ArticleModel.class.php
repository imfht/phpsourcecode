<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

// 文章模型
class ArticleModel extends CommonModel {
	protected $_validate	 =	 array(
		array('title','require','标题必须！'),
		array('content','require','内容必须'),
	);

	protected $_auto	 =	 array(
		array('status','1',self::MODEL_INSERT,'string'),
		array('create_time','create_time',self::MODEL_BOTH,'callback'),
        array('update_time','time',self::MODEL_BOTH,'function'),
	);

	// 在下面添加需要的数据访问方法
	public function top($condition)
	{
        if(FALSE === $this->where($condition)->setField('is_top',array('exp','(1-is_top)'))){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            return True;
        }
	}

}
?>