<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

/**
 * 菜单模型
 */
class Menu extends AdminBase
{
    protected $insert = ['create_time'=>TIME_NOW];
	protected $auto = ['update_time'=>TIME_NOW];
	protected $update = ['update_time'=>TIME_NOW];
    /**
     * 隐藏状态获取器
     */
    public function getIsHideTextAttr()
    {
        
        $is_hide = [0 => '否', 1 => '是'];
        
        return $is_hide[$this->data['is_hide']];
    }
}
