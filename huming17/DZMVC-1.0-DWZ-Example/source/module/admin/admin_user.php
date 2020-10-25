<?php

/**
 * 用户管理
 * @author HumingXu E-mail:huming17@126.com
 */
switch ($action) {
    case "delete":
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
        if($user_id){
            //DEBUG 删除
            DB::update("users", array("isdelete" => 1), array("user_id" => $user_id));
            echo '{
                "statusCode":"200",
                "message":"'.lang('core','operation_successful').'",
                "navTabId":"",
                "rel":"",
                "callbackType":"forward",
                "forwardUrl":"admin.php?mod=user&action=index",
                "confirmMsg":""
            }';
        }
        break;
    
    case "check_user_name":
        $check_user = array();
        $user_name = isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : '';
        $modify_user_id = isset($_REQUEST['modify_user_id']) ? $_REQUEST['modify_user_id'] : '';
        if(!empty($user_name)){
            $check_user = ext::check_user_exist($user_name,$modify_user_id);
            if(empty($check_user)){
                echo "true";
            }else{
                echo "false";
            }
        }else{
            echo "false";
        }
        die();
        break;
    
    case "add":
    case "edit":
        $user_date = array();
        $user_password_encode = '';
        $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;
        $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
        $user_realname = isset($_POST['user_realname']) ? $_POST['user_realname'] : '';
        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $user_password2 = isset($_POST['user_password2']) ? $_POST['user_password2'] : '';
        $user_role_id = isset($_POST['lookup_role_user_role_id']) ? $_POST['lookup_role_user_role_id'] : '';
		$user_score = isset($_POST['user_score']) ? $_POST['user_score'] : '';
        
        //DEBUG 用户菜单权限
        $user_menu = isset($_POST['user_menu']) ? $_POST['user_menu'] : '';
        $user_menu_count = $return_user_id = 0;
        
        //DEBUG 取出编辑用户权限菜单start
        $user_menu_current_array = ext::get_user_role_menu(2, $user_id, 1);

        //DEBUG 取出所有菜单 start (TODO:菜单数量超过500,增加菜单缓存)
        $menu_array = $user_menu_array = array();
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
            if(array_key_exists($mvalue['menu_id'],$user_menu_current_array)){
                $menu_open_selected = ", checked:true ";
            }else{
                $menu_open_selected = "";
            }
            $menu_json_string .= '{ id:'.$mvalue['menu_id'].', pId:'.$mvalue['menu_pid'].', name:"'.$mvalue['name_var'].'"'.$menu_open_selected.$menu_open.'}';
            $i++;
        }
        $menu_json_string = '['.$menu_json_string.']';
        //DEBUG 取出所有菜单 end
        if($user_id){
            //DEBUG 编辑
            $user_result = ext::getuserbyuid($user_id);
            $user_result['role_name'] = ext::role_name($user_result['user_role_id']);
            if($is_submit){
                if(($user_password == $user_password2) && !empty($user_name) && !empty($user_realname)){
                    $user_data = array(
                        "user_name" => $user_name,
                        "user_realname" => $user_realname,
                        "user_role_id" => $user_role_id,
						"user_score" => $user_score,
                        "user_modify_time" => TIMESTAMP
                     );
                    if(!empty($user_password)){
                        $user_password_encode = encode_password($user_password);
                        $user_data["user_password"] = $user_password_encode;
                    }
                    DB::update('users', $user_data, "user_id='".$user_id."' LIMIT 1");
                    //DEBUG 更新用户权限菜单
                    ext::set_user_role_menu(2,$user_menu,$user_id);
                    echo '{
                        "statusCode":"200",
                        "message":"'.lang('core','operation_successful').'",
                        "navTabId":"admin_user_index",
                        "rel":"",
                        "reloadFlag":"1",
                        "callbackType":"closeCurrent",
                        "forwardUrl":"",
                        "confirmMsg":""
                    }';
                }else{
                    echo '{
                        "statusCode":"300",
                        "message":"'.lang('core','operation_failed').'",
                        "navTabId":"admin_user_index",
                        "rel":"",
                        "reloadFlag":"1",
                        "callbackType":"closeCurrent",
                        "forwardUrl":"",
                        "confirmMsg":""
                    }';
                }
                die();
            } 
        }else{
            //DEBUG 新增
            if($is_submit){
                if(($user_password == $user_password2) && !empty($user_name) && !empty($user_realname)){
                    $user_password_encode = encode_password($user_password);
                    $user_data = array(
                        "user_name" => $user_name,
                        "user_realname" => $user_realname,
                        "user_password" => $user_password_encode,
                        "user_role_id" => $user_role_id,
						"user_score" => $user_score,
                        "user_create_time" => TIMESTAMP
                    );
                    DB::insert('users', $user_data);
                    //DEBUG 更新用户权限菜单
                    ext::set_user_role_menu(2,$user_menu,$user_id);
                    echo '{
                        "statusCode":"200",
                        "message":"'.lang('core','operation_successful').'",
                        "navTabId":"admin_user_index",
                        "rel":"",
                        "reloadFlag":"1",
                        "callbackType":"closeCurrent",
                        "forwardUrl":"",
                        "confirmMsg":""
                    }';
                }else{
                    echo '{
                        "statusCode":"300",
                        "message":"'.lang('core','operation_failed').'",
                        "navTabId":"admin_user_index",
                        "rel":"",
                        "reloadFlag":"1",
                        "callbackType":"closeCurrent",
                        "forwardUrl":"",
                        "confirmMsg":""
                    }';
                }
                die();
            }
        }

        include template('admin/user/user_edit');
        break;
    
    default:
        //DEBUG 查询并返回
        $page_array = array();
        $sqlcount = $sql = $wheresql = $user_name_keyword = $pageNum = $numPerPage = '';
        $user_name_keyword = isset($_REQUEST['user_name_keyword']) ? $_REQUEST['user_name_keyword'] : '';
        $pageNum = isset($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
        $numPerPage = isset($_REQUEST['numPerPage']) ? $_REQUEST['numPerPage'] : 10;
        $pagestart = ($pageNum - 1) * $numPerPage;
        if($user_name_keyword){
            $wheresql .= " AND users.user_name LIKE '%".$user_name_keyword."%'";
        }
        $sqlcount = "SELECT count(*) from ".DB::table('users')." AS users WHERE users.isdelete=0 ".$wheresql;
        $pagetotal = DB::result_first($sqlcount);
        $sql = "SELECT users.*, role.role_name from ".DB::table('users')." AS users LEFT JOIN ".DB::table('user_role')." AS role ON users.user_role_id = role.role_id WHERE users.isdelete=0 ".$wheresql." ORDER BY users.user_id DESC LIMIT ".$pagestart.",".$numPerPage;
        $page_array = DB::fetch_all($sql);
        include template('admin/user/user');
        break;
}
?>