<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

use app\admin\logic\Menu as LogicMenu;

/**
 * 授权逻辑
 */
class AuthGroupAccess extends AdminBase
{
    
    /**
     * 获得权限菜单列表
     */
    public function getAuthMenuList($member_id = 0)
    {
        
        $model = get_sington_object('menuLogic', LogicMenu::class);
       
        if (IS_ROOT) : return $model->getMenuList(); endif;
        
      
        // 获取用户组列表
        $group_list = $this->getMemberGroupInfo($member_id);
       
        
        $menu_ids = [];
        
        foreach ($group_list as $group_info) {
            
            // 合并多个分组的权限节点并去重
            !empty($group_info['rules']) && $menu_ids = array_unique(array_merge($menu_ids, explode(',', trim($group_info['rules'], ','))));
        }
        
        // 若没有权限节点则返回
        if (empty($menu_ids)) : return $menu_ids; endif;
        
        // 查询条件
        $where = ['id' => ['in', $menu_ids]];
        
        return $model->getMenuList($where);
    }
    
    /**
     * 获得权限菜单URL列表
     */
    public function getAuthMenuUrlList($auth_menu_list = [])
    {
        
        $auth_list = [];
        
        foreach ($auth_menu_list as $info) {
            
            $auth_list[] = $info['module'] . SYS_DSS . $info['url'];
        }

        return $auth_list;
    }
    
    /**
     * 获取会员所属权限组信息
     */
    public function getMemberGroupInfo($member_id = 0)
    {
        
        $model = model($this->name);
        
       // $model->alias('a');

        $where['m.member_id'] = $member_id;
        $where['m.status']    = DATA_NORMAL;
        
        $field = 'm.member_id, m.group_id, g.name, g.describe, g.rules';
        
        $join = [
                    [DB_PREFIX.'auth_group g', 'm.group_id = g.id'],
                ];
       
        return $model->getList($where, $field, '', false, $join);
    }
    
}
