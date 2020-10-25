<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\behavior;

use think\Hook;

/**
 * 初始化钩子信息行为
 */
class InitHook
{

    /**
     * 行为入口
     */
    public function run()
    {
        
        $HookModel  = model(SYS_COMMON_DIR_NAME . SYS_DSS . ucwords(SYS_HOOK_DIR_NAME));
        
        $AddonModel = model(SYS_COMMON_DIR_NAME . SYS_DSS . ucwords(SYS_ADDON_DIR_NAME));
        
        $hook_list = $HookModel->column('name,addon_list');

        foreach ($hook_list as $k => $v)
        {
            
            if (!empty($v)):
                
            $where[DATA_COMMON_STATUS] = DATA_NORMAL;
            $name_list = explode(',', $v);
            $where['name'] = ['in', $name_list];

            $data = $AddonModel->where($where)->column('id,name'); 

            !empty($data) && Hook::add($k, array_map('get_addon_class', array_intersect($name_list, $data)));
            
            endif;
        }
    }
}
