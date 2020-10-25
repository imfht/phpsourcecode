<?php
// +----------------------------------------------------------------------
// | Author: 战神巴蒂<378020023@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;


class AlbumTypeModel extends Model {
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE ,'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
    );

}
