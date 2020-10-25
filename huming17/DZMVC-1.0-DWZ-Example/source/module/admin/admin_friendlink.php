<?php

/**
 * 信息管理
 * @author HumingXu E-mail:huming17@126.com
 */
switch ($do) {
    case "delete":
        $fl_id = isset($_REQUEST['fl_id']) ? $_REQUEST['fl_id'] : '';
        if($fl_id){
            //DEBUG 更新isdelete=1 字段作为删除
            //DB::delete("content", array("fl_id" => $fl_id), $limit=1);
            DB::update("friendlink", array("isdelete" => 1), array("fl_id" => $fl_id));
            echo '{
                "statusCode":"200",
                "message":"操作成功",
                "navTabId":"",
                "rel":"",
                "callbackType":"forward",
                "forwardUrl":"admin.php?mod=friendlink&action=index",
                "confirmMsg":""
            }';
        }
        break;
    
    case "add":
    case "edit":
        $name_var = $fl_url = '';
        $fl_id = isset($_REQUEST['fl_id']) ? $_REQUEST['fl_id'] : '';
        if($fl_id){
            //DEBUG 编辑更新信息
            $sql = "SELECT * FROM ".DB::table('friendlink')." WHERE fl_id='".$fl_id."' LIMIT 1";
            $result = DB::fetch_first($sql);
            $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
            if($is_submit){
                $fl_title = isset($_POST['fl_title']) ? $_POST['fl_title'] : '';
                $fl_url = isset($_POST['fl_url']) ? $_POST['fl_url'] : '';
                DB::update('friendlink', 
                        array(
                            "fl_title" => $fl_title,
                            "fl_url" => $fl_url,
                            'modify_dateline' => TIMESTAMP
                        ),
                        "fl_id='".$fl_id."' LIMIT 1"
                );
                echo '{
                    "statusCode":"200",
                    "message":"操作成功2",
                    "navTabId":"admin_friendlink_index",
                    "rel":"",
                    "reloadFlag":"1",
                    "callbackType":"closeCurrent",
                    "forwardUrl":"",
                    "confirmMsg":""
                }';
                die();
            } 
        }else{
            //DEBUG 新增信息
            $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
            if($is_submit){
                $fl_title = isset($_POST['fl_title']) ? $_POST['fl_title'] : '';
                $fl_url = isset($_POST['fl_url']) ? $_POST['fl_url'] : '';
                $data = array(
                    "fl_title" => $fl_title,
                    "fl_url" => $fl_url,
                    'create_dateline' => TIMESTAMP
                );
                DB::insert('friendlink', $data);
                echo '{
                    "statusCode":"200",
                    "message":"操作成功",
                    "navTabId":"admin_friendlink_index",
                    "rel":"",
                    "reloadFlag":"1",
                    "callbackType":"closeCurrent",
                    "forwardUrl":"",
                    "confirmMsg":""
                }';
                die();
            }
        }

        include template('admin/friendlink/edit');
        break;
    
    default:
        //DEBUG 查询并返回信息链接
        $page_array = array();
        $sqlcount = $sql = $wheresql = $title_keyword = $pageNum = $numPerPage = '';
        $title_keyword = isset($_REQUEST['title_keyword']) ? $_REQUEST['title_keyword'] : '';
        $pageNum = isset($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
        $numPerPage = isset($_REQUEST['numPerPage']) ? $_REQUEST['numPerPage'] : 10;
        $pagestart = ($pageNum - 1) * $numPerPage;
        if($title_keyword){
            $wheresql .= " AND fl_title LIKE '%".$title_keyword."%'";
        }
        $sqlcount = "SELECT count(*) from ".DB::table('friendlink')." WHERE isdelete=0 ".$wheresql;
        $pagetotal = DB::result_first($sqlcount);
        $sql = "SELECT * from ".DB::table('friendlink')." WHERE isdelete=0 ".$wheresql." ORDER BY fl_id DESC LIMIT ".$pagestart.",".$numPerPage;
        $page_array = DB::fetch_all($sql);
        include template('admin/friendlink/index');
        break;
}
?>