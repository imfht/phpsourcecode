<?php

namespace app\admin\model;

use app\common\model\ModelBase;

class Menu extends ModelBase
{

    public function getIsHideTextAttr($is_hide)
    {

        $hidearr = [0 => '否', 1 => '是'];

        return $hidearr[$is_hide];

    }

    public function getStatusTextAttr($status)
    {

        $statusarr = [DATA_DELETE => '删除', DATA_DISABLE => '禁用', DATA_NORMAL => '启用', 3 => '认证'];

        return $statusarr[$status];

    }


}
