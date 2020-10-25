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
    public static $menuModel = null;


    // 菜单Select结构
    public static $menuSelect = [];

    /**
     * 构造方法
     */
    public function __construct()
    {
        parent::__construct();

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
            if (!empty($info[$name])) {
                $info[$name] = empty($level) || empty($info['pid']) ? $info[$name] . "&nbsp;" : $tmp_str . $info[$name] . "&nbsp;";

                if (!array_key_exists($child, $info)) {

                    array_push(self::$menuSelect, $info);
                } else {


                    $tmp_ary = $info[$child];

                    unset($info[$child]);

                    array_push(self::$menuSelect, $info);

                    $this->menuToSelect($tmp_ary, 1, $name, $child);
                }
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

        $auth_group_info = $this->setname('AuthGroup')->getDataInfo(['id' => $id], 'rules');

        $rules_array = explode(',', $auth_group_info['rules']);


        //遍历菜单列表
        foreach ($menu_list as $menu_info) {

            $icon = empty($menu_info['icon']) ? 'fa-dot-circle-o' : $menu_info['icon'];

            $checkbox_select = in_array($menu_info['id'], $rules_array) ? "checked='checked'" : '';

            $title = $menu_info['name'];

            if (!empty($menu_info[$child])) {

                $menu_view .= "<div class='box box-header admin-node-header'>
                                          <div class='box-header'><div class='checkbox'> 
                                                  <input class='rules_all' type='checkbox' name='rules[]' title='" . $title . "' value='" . $menu_info['id'] . "' " . $checkbox_select . " >  </div></div><div class='box-body'>" . $this->menuToCheckboxView($menu_info[$child], $child) . "</div></div>";


            } else {

                $menu_view .= "<div class='admin-node-label'>  <input type='checkbox' name='rules[]' title='" . $title . "' value='" . $menu_info['id'] . "'  " . $checkbox_select . " > </div>";
            }
        }

        return $menu_view;
    }


    /**
     * 获取菜单列表
     */
    public function getMenuList($where = [], $field = true, $order = 'pid asc,sort desc', $paginate = false)
    {


        $data = $this->getDataList($where, $field, $order, $paginate, '', '', '', true);


        return $data;

    }


    /**
     * 获取菜单信息
     */
    public function getMenuInfo($where = [], $field = true)
    {

        $data = $this->getDataInfo($where, $field);

        return $data;
    }

    /**
     * 菜单添加
     */
    public function menuAdd($data = [])
    {


        $url = es_url('menuList', ['pid' => $data['pid'] ? $data['pid'] : 0]);

        return $this->dataAdd($data, $isvalidate = true, $url, '菜单添加成功');
    }

    /**
     * 菜单编辑
     */
    public function menuEdit($data = [])
    {

        $url = es_url('menuList', ['pid' => $data['pid'] ? $data['pid'] : 0]);

        $where['id'] = $data['id'];

        return $this->dataEdit($data, $where, $isvalidate = true, $url, '菜单编辑成功');
    }

    /**
     * 菜单删除
     */
    public function menuDel($where = [])
    {

        return $this->dataDel($where) ? [RESULT_SUCCESS, '菜单删除成功'] : [RESULT_ERROR, $this->getError()];
    }


}
