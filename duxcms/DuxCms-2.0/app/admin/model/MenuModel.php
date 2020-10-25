<?php
namespace app\admin\model;
/**
 * 后台菜单
 */
class MenuModel {
	/**
     * 获取所有菜单
     */
	public function getMenu($loginUserInfo = array(),$cutUrl = '',$urlComplete = true){
        if(!empty($loginUserInfo)){
            $menuPurview = unserialize($loginUserInfo['menu_purview']);
        }
        $list = array();
		$list = get_all_service('Menu','Admin');
		//合并菜单
        foreach ($list as $value) {
            $menu=array_merge_recursive((array)$menu,(array)$value);
        }
        //排序菜单
        foreach ((array) $menu as $topKey => $top) {
            if (!empty($top['menu']) && is_array($top['menu'])) {    
                if(!empty($menuPurview)&&$top['menu']&&$loginUserInfo['user_id']<>1){
                    $subMenu = array();            
                    foreach ($top['menu'] as $vo) {
                        if(in_array($top['name'].'_'.$vo['name'], $menuPurview)){
                            $subMenu[] = $vo;
                        }
                    }
                    $top['menu'] = $subMenu;
                }
                $menu[$topKey]['menu'] = array_order($top['menu'], 'order', 'asc');
            }
        }
        $menuList = array_order($menu, 'order', 'asc');
        return $menuList;
        //递归循环
        if(!empty($cutUrl)){
            if($urlComplete){
                $url = $_SERVER["SCRIPT_NAME"]. '?r='.$cutUrl;
            }else{
                $url = $cutUrl;
            }
        }else{
            $url = $_SERVER["SCRIPT_NAME"] . '?r='.APP_NAME .'/' . CONTROLLER_NAME;
        }

        $urlLen = strlen($url);
        foreach ($menuList as $k => $list) {
            if(!empty($list)){
                foreach ($list['menu'] as $key => $value) {
                    if(!$menuList[$k]['url']){
                        $menuList[$k]['url'] = $value['url'];
                    }
                    if(substr($value['url'], 0,$urlLen) == $url){
                        //获取当前菜单于高亮
                        $curList = $list;
                        $curList['menu'][$key]['cur'] = 1;
                        $menuList[$k]['cur'] = 1;
                    }
                }
            }
        }
        $menu = array();
        $menu['list'] = $menuList;
        $menu['curList'] = $curList;
        return $menu;
	}
    /**
     * 获取所有操作
     */
    public function getPurview(){
        $list = get_all_service('Purview','Admin');
        if(empty($list)){
            return $list;
        }
        return $list;
    }
}