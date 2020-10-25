<?php 

function my_menu(){
    $where = [];
    if (!\think\Session::get('super_admin')) {
        // 根据角色获取有权限的菜单id
        if ($group_ids = \think\Db::name('auth_access')->where('uid', 'eq', \think\Session::get('manager_id'))->column('group_id')) {
            $menu_ids = \think\Db::name('auth_group')->where(['id' => ['in', $group_ids]])->column('menus');
            $menu_ids = implode(',', $menu_ids);
            $menu_ids = array_unique(explode(',', $menu_ids));
            if ($menu_ids) {
                $where['id'] = ['in', $menu_ids];
            } else {
                $where['id'] = ['eq', -1];
            }
        }
    }
    $data = \think\Db::name('menu')->where($where)->order('sort desc,id asc')->select();
    foreach ($data as &$v) {
        $url = htmlspecialchars_decode($v['url']);
        if (0 === strpos($url, 'javascript:')) {
            $v['url'] = $url;
        }else{
            $v['url'] = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : \think\Url::build($url);
        }
    }
    return $data;
}