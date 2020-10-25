<?php

/**
 * 角色管理
 * @author HumingXu E-mail:huming17@126.com
 */
switch ($action) {
    case "delete":
        $role_id = isset($_REQUEST['role_id']) ? $_REQUEST['role_id'] : '';
        if($role_id){
            DB::update("user_role", array("isdelete" => 1), array("role_id" => $role_id));
            echo '{
                "statusCode":"200",
                "message":"操作成功",
                "navTabId":"admin_user_role_index",
                "rel":"",
                "callbackType":"forward",
                "forwardUrl":"admin.php?mod=user_role&action=index",
                "confirmMsg":""
            }';
        }
        break;
    
    case "add":
    case "edit":
        $role_id = isset($_REQUEST['role_id']) ? $_REQUEST['role_id'] : '';
        $role_name = isset($_POST['role_name']) ? $_POST['role_name'] : '';
        $user_role_menu = isset($_POST['user_role_menu']) ? $_POST['user_role_menu'] : '';
        $user_role_menu_count = $return_role_id = 0;
        $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
        
        //DEBUG 取出编辑角色权限菜单 简单数组
        $user_role_menu_current_array = ext::get_user_role_menu(1, $role_id, 1);

        //DEBUG 取出所有菜单 start (TODO:菜单数量超过500,增加菜单缓存)
        $menu_array = $user_role_menu_array = array();
        $menu_open_selected = $menu_open = $menu_json_string = $sql = '';
        $sql = "SELECT menu_id,menu_pid,name_var from ".DB::table('common_menu')." WHERE enable=1 AND isdelete=0";
        $menu_array = DB::fetch_all($sql);
        $i = 1;
        foreach($menu_array AS $mkey => $mvalue){
            if($i > 1){
                $menu_json_string .= ',';
            }
            //DEBUG 顶部菜单打开
            if($mvalue['menu_pid'] == 1 || $mvalue['menu_pid'] == 0){
                $menu_open = ", open:true";
            }else{
                $menu_open = "";
            }
            if(array_key_exists($mvalue['menu_id'],$user_role_menu_current_array)){
                $menu_open_selected = ", checked:true ";
            }else{
                $menu_open_selected = "";
            }
            $menu_json_string .= '{ id:'.$mvalue['menu_id'].', pId:'.$mvalue['menu_pid'].', name:"'.$mvalue['name_var'].'"'.$menu_open_selected.$menu_open.'}';
            $i++;
        }
        $menu_json_string = '['.$menu_json_string.']';
        //DEBUG 取出所有菜单 end

        if($is_submit){
            if($role_id){
                //DEBUG 更新信息
                DB::update('user_role', array("role_name" => $role_name), "role_id='".$role_id."' LIMIT 1");
                echo '{
                    "statusCode":"200",
                    "message":"操作成功",
                    "navTabId":"admin_user_role_index",
                    "rel":"",
                    "reloadFlag":"1",
                    "callbackType":"closeCurrent",
                    "forwardUrl":"admin.php?mod=user_role&action=index",
                    "confirmMsg":""
                }';
            }else{
                //DEBUG 新增信息
                $role_data = array(
                    "role_name" => $role_name
                );
                $role_id = DB::insert('user_role', $role_data, $return_insert_id = true);
                echo '{
                    "statusCode":"200",
                    "message":"操作成功",
                    "navTabId":"admin_user_role_index",
                    "rel":"",
                    "reloadFlag":"1",
                    "callbackType":"closeCurrent",
                    "forwardUrl":"admin.php?mod=user_role&action=index",
                    "confirmMsg":""
                }';
            }
            ext::set_user_role_menu(1,$user_role_menu,$role_id);
            die();
        }else{
            if($role_id){
                //DEBUG 取出编辑用户信息
                $role_sql = "SELECT role_id,role_name FROM ".DB::table('user_role')." WHERE role_id='".$role_id."' LIMIT 1";
                $role_result = DB::fetch_first($role_sql); 
            }
        }
        include template('admin/user/user_role_edit');
        break;
        
    case "lookup_role";
        $wheresql ='';
        $role_results = array();
        $sql = "SELECT role_id, role_name from ".DB::table('user_role')." WHERE isdelete=0 ".$wheresql;
        $role_results = DB::fetch_all($sql);
        include template('admin/user/user_role_tree');
        break;
    
    default:
        //DEBUG 查询并返回信息链接
        $page_array = array();
        $sqlcount = $sql = $wheresql = $role_name_keyword = $pageNum = $numPerPage = '';
        $role_name_keyword = isset($_REQUEST['role_name_keyword']) ? $_REQUEST['role_name_keyword'] : '';
        $pageNum = isset($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
        $numPerPage = isset($_REQUEST['numPerPage']) ? $_REQUEST['numPerPage'] : 10;
        $pagestart = ($pageNum - 1) * $numPerPage;
        if($role_name_keyword){
            $wheresql .= " AND role_name LIKE '%".$role_name_keyword."%'";
        }
        $sqlcount = "SELECT count(*) from ".DB::table('user_role')." WHERE isdelete=0 ".$wheresql;
        $pagetotal = DB::result_first($sqlcount);
        $sql = "SELECT role_id, role_name from ".DB::table('user_role')." WHERE isdelete=0 ".$wheresql." ORDER BY role_id DESC LIMIT ".$pagestart.",".$numPerPage;
        $page_array = DB::fetch_all($sql);
        include template('admin/user/user_role');
        break;
}
?>