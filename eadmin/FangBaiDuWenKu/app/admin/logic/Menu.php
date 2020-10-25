<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 菜单逻辑
 */
class Menu extends AdminBase
{
    
    // 菜单模型
    public static $menuModel    = null;
    
    // 面包屑
    public static $crumbs       = [];
    
    // 菜单Select结构
    public static $menuSelect   = [];
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$menuModel = model($this->name);
    }
    

    
    /**
     * 菜单转Select
     */
    public function menuToSelect($menu_list = [], $level = 0, $name = 'name', $child = 'children')
    {
       
        foreach ($menu_list as $info) {
            
            $tmp_str = str_repeat("&nbsp;", $level * 4);
           
            $tmp_str .= "├";

            $info['level'] = $level;
            
            $info[$name] = empty($level) || empty($info['pid']) ? $info[$name]."&nbsp;" : $tmp_str . $info[$name] . "&nbsp;";
            
            if (!array_key_exists($child, $info)) {

                array_push(self::$menuSelect, $info);
            } else {
                
              
                $tmp_ary = $info[$child];
                  
                unset($info[$child]);
                
                array_push(self::$menuSelect, $info);
                
                $this->menuToSelect($tmp_ary, 1, $name, $child);
            }
        }
        
        return self::$menuSelect;
    }
    
    /**
     * 菜单转Checkbox
     */
    public function menuToCheckboxView($menu_list = [], $child = 'children')
    {
        
        $menu_view = '';
        
        $id = input('id');
        
        $auth_group_info = model('AuthGroup', LAYER_LOGIC_NAME)->getGroupInfo(['id' => $id], 'rules');
        
        $rules_array = str2arr($auth_group_info['rules']);
        
        //遍历菜单列表
        foreach ($menu_list as $menu_info) {
            
            $icon = empty($menu_info['icon']) ? 'fa-dot-circle-o' : $menu_info['icon'];
            
            $checkbox_select = in_array($menu_info['id'], $rules_array) ? "checked='checked'" : '';
            
            $title=$menu_info['name'];
            
            if (!empty($menu_info[$child])) {
                
                $menu_view.=  "<div class='box box-header admin-node-header'>
                                          <div class='box-header'><div class='checkbox'> 
                                                  <input class='rules_all' type='checkbox' name='rules[]' title='".$title."' value='".$menu_info['id']."' ".$checkbox_select." >  </div></div><div class='box-body'>".$this->menuToCheckboxView($menu_info[$child],  $child)."</div></div>";
                                    
                
            } else {
                
                $menu_view.=    "<div class='admin-node-label'>  <input type='checkbox' name='rules[]' title='".$title."' value='".$menu_info['id']."'  ".$checkbox_select." > </div>";
            }
       }
       
       return $menu_view;
    }
    
    /**
     * 菜单选择
     */
    public function selectMenu($menu_view = '')
    {
        
        $map['url']    = URL;
        $map['module'] = MODULE_NAME;
                
        $menu_info = $this->getMenuInfo($map);
        
        // 获取自己及父菜单列表
        $this->getParentMenuList($menu_info['id']);
 
        // 选中面包屑中的菜单
        foreach (self::$crumbs as $menu_info) {
            
            $replace_data = "menu_id='".$menu_info['id']."'";
            
            $menu_view = str_replace($replace_data, " class='active' ", $menu_view);
        }
        
       return $menu_view;
    }
    
    /**
     * 获取自己及父菜单列表
     */
    public function getParentMenuList($menu_id = 0)
    {
        
        $menu_info = $this->getMenuInfo(['id' => $menu_id]);
        
        !empty($menu_info['pid']) && $this->getParentMenuList($menu_info['pid']);
        
        self::$crumbs [] = $menu_info;
    }
    
    /**
     * 获取面包屑
     */
    public function getCrumbsView()
    {
        
        $crumbs_view = "<ol class='breadcrumb'>";
      
        foreach (self::$crumbs as $menu_info) {
            
            $icon = empty($menu_info['icon']) ? 'fa-circle-o' : $menu_info['icon'];
            
            $crumbs_view .= "<li><a><i class='fa $icon'></i> ".$menu_info['name']."</a></li>";
        }
        
        $crumbs_view .= "</ol>";
        
        return $crumbs_view;
    }
    
    /**
     * 获取菜单列表
     */
    public function getMenuList($where = [], $field = true, $order = 'pid asc,sort desc', $paginate = false)
    {
        
        return self::$menuModel->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 获取菜单信息
     */
    public function getMenuInfo($where = [], $field = true)
    {
        
        return self::$menuModel->getInfo($where, $field);
    }
    
    /**
     * 菜单添加
     */
    public function menuAdd($data = [])
    {
        
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('add')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('menuList', ['pid' => $data['pid'] ? $data['pid'] : 0]);
        
        return self::$menuModel->setInfo($data) ? [RESULT_SUCCESS, '菜单添加成功', $url] : [RESULT_ERROR, self::$menuModel->getError()];
    }
    
    /**
     * 菜单编辑
     */
    public function menuEdit($data = [])
    {
        
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('edit')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('menuList', ['pid' => $data['pid'] ? $data['pid'] : 0]);
        
        return self::$menuModel->setInfo($data) ? [RESULT_SUCCESS, '菜单编辑成功', $url] : [RESULT_ERROR, self::$menuModel->getError()];
    }
    
    /**
     * 菜单删除
     */
    public function menuDel($where = [])
    {
        
        return self::$menuModel->deleteInfo($where) ? [RESULT_SUCCESS, '菜单删除成功'] : [RESULT_ERROR, self::$menuModel->getError()];
    }
        /**
     * 菜单批量删除
     */
    public function menuAlldel($ids)
    {
    	

    return self::$menuModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '菜单删除成功'] : [RESULT_ERROR, self::$menuModel->getError()];
    }  
    /**
     * 获取默认页面标题
     */
    public function getDefaultTitle()
    {
        
        return self::$menuModel->getValue(['module' => MODULE_NAME, 'url' => URL], 'name');
    }
}
