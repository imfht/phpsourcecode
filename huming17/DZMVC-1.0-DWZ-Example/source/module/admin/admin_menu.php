<?php

/**
 * 菜单管理
 * @author HumingXu E-mail:huming17@126.com
 */
switch ($do) {
    case "delete":
        $menu_id = isset($_REQUEST['menu_id']) ? $_REQUEST['menu_id'] : '';
        if($menu_id){
            DB::update("common_menu", array("isdelete" => 1,"enable" => 0), array("menu_id" => $menu_id));
            echo '{
                "statusCode":"200",
                "message":"操作成功",
                "navTabId":"",
                "rel":"",
                "callbackType":"forward",
                "forwardUrl":"admin.php?mod=menu&action=index",
                "confirmMsg":""
            }';
        }
        break;
    
    case "add":
    case "edit":
        $name_var = $info_url = '';
        $menu_id = isset($_REQUEST['menu_id']) ? $_REQUEST['menu_id'] : '';
        if($menu_id){
            //DEBUG 编辑更新菜单
            $menu_sql = "SELECT * FROM ".DB::table('common_menu')." WHERE menu_id='".$menu_id."' LIMIT 1";
            $menu_result = DB::fetch_first($menu_sql);
            $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
            if($is_submit){
                $name_var = isset($_POST['name_var']) ? $_POST['name_var'] : '';
                $info_url = isset($_POST['lookup_info_url']) ? $_POST['lookup_info_url'] : '';
                DB::update('common_menu', array("name_var" => $name_var, "url" => $info_url), "menu_id='".$menu_id."' LIMIT 1");
                echo '{
                    "statusCode":"200",
                    "message":"操作成功",
                    "navTabId":"",
                    "rel":"",
                    "callbackType":"forward",
                    "forwardUrl":"admin.php?mod=menu&action=index",
                    "confirmMsg":""
                }';
                die();
            } 
        }else{
            //DEBUG 新增菜单
            $menu_pid = isset($_REQUEST['menu_pid']) ? $_REQUEST['menu_pid'] : '';
            $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
            if($is_submit){
                $name_var = isset($_POST['name_var']) ? $_POST['name_var'] : '';
                $info_url = isset($_POST['lookup_info_url']) ? $_POST['lookup_info_url'] : '';
                $position = isset($_POST['position']) && !empty($_POST['position']) ? $_POST['position'] : md5($info_url);
                $menu_data = array(
                    "menu_pid" => $menu_pid,
                    "position" => $position,
                    "sub_position" => '',
                    "name_var" => $name_var,
                    "url" => $info_url
                );
                DB::insert('common_menu', $menu_data,true);
                echo '{
                    "statusCode":"200",
                    "message":"操作成功",
                    "navTabId":"",
                    "rel":"",
                    "callbackType":"forward",
                    "forwardUrl":"admin.php?mod=menu&action=index",
                    "confirmMsg":""
                }';
                die();
            }
        }

        include template('admin/menu/menu_edit');
        break;
    
    case "lookup_info":
        //DEBUG 查询并返回信息链接
        $page_array = array();
        $sqlcount = $sql = $wheresql = $title_keyword = $pageNum = $numPerPage = '';
        $title_keyword = isset($_REQUEST['title_keyword']) ? $_REQUEST['title_keyword'] : '';
        $pageNum = isset($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
        $numPerPage = isset($_REQUEST['numPerPage']) ? $_REQUEST['numPerPage'] : 10;
        $pagestart = ($pageNum - 1) * $numPerPage;
        if($title_keyword){
            $wheresql .= " AND title LIKE '%".$title_keyword."%'";
        }
        $sqlcount = "SELECT count(*) from ".DB::table('content')." WHERE isdelete=0 ".$wheresql;
        $pagetotal = DB::result_first($sqlcount);
        $sql = "SELECT info_id,title from ".DB::table('content')." WHERE isdelete=0 ".$wheresql." ORDER BY info_id DESC LIMIT ".$pagestart.",".$numPerPage;
        $page_array = DB::fetch_all($sql);
        include template('admin/menu/lookup_info');
        break;
    
    default:
        //DEBUG 取出所有菜单(TODO:菜单数量超过500,增加菜单缓存)
        $menu_array = array();
        $menu_json_string = $sql = '';
        $menu_open = '';
        $sql = "SELECT menu_id,menu_pid,name_var from ".DB::table('common_menu')." WHERE enable=1 AND isdelete=0";
        $menu_array = DB::fetch_all($sql);
        $i = 1;
        foreach($menu_array AS $mkey => $mvalue){
            if($i > 1){
                $menu_json_string .= ',';
            }
            //DEBUG 顶部菜单打开
            if($mvalue['menu_pid'] == 1 || $mvalue['menu_pid'] == 0){
                $menu_open = ', open:true';
            }else{
                $menu_open = "";
            }
            $menu_json_string .= '{ id:'.$mvalue['menu_id'].', pId:'.$mvalue['menu_pid'].', name:"'.$mvalue['name_var'].'"'.$menu_open.'}';
            $i++;
        }
        $menu_json_string = '['.$menu_json_string.']';
        include template('admin/menu/menu');
}
?>