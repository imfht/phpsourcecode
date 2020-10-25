<?php

/**
 * 信息管理
 * @author HumingXu E-mail:huming17@126.com
 */
switch ($do) {
    case "delete":
        $info_cateid = isset($_REQUEST['info_cateid']) ? $_REQUEST['info_cateid'] : '';
        if($info_cateid){
            //DEBUG 更新isdelete=1 字段作为删除
            //DB::delete("content", array("info_id" => $info_id), $limit=1);
            DB::update("content_cate", array("isdelete" => 1), array("info_cateid" => $info_cateid));
            echo '{
                "statusCode":"200",
                "message":"操作成功",
                "navTabId":"admin_info_cate_index",
                "rel":"",
                "callbackType":"forward",
                "forwardUrl":"admin.php?mod=info_cate&do=index",
                "confirmMsg":""
            }';
        }
        break;
    
    case "add":
    case "edit":
        $name_var = $info_url = '';
        $info_cateid = isset($_REQUEST['info_cateid']) ? $_REQUEST['info_cateid'] : '';
        if($info_cateid){
            //DEBUG 编辑更新信息
            $info_sql = "SELECT * FROM ".DB::table('content_cate')." WHERE info_cateid='".$info_cateid."' LIMIT 1";
            $info_result = DB::fetch_first($info_sql);
            $info_result['p_tilte'] = get_title_by_info_cateid($info_result['info_catepid']);
            $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
            if($is_submit){
                $info_catepid = isset($_POST['lookup_cate_info_cateid']) ? $_POST['lookup_cate_info_cateid'] : 0;
                $relate_info_cateid = isset($_POST['lookup_cate_relate_info_cateid']) ? $_POST['lookup_cate_relate_info_cateid'] : 0;
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $cate_desc = isset($_POST['cate_desc']) ? $_POST['cate_desc'] : '';
                DB::update('content_cate', array("info_catepid" => $info_catepid, "relate_info_cateid" => $relate_info_cateid, "title" => $title, 'cate_desc'=> $cate_desc, 'modify_dateline'=> TIMESTAMP), "info_cateid='".$info_cateid."' LIMIT 1");
                echo '{
                    "statusCode":"200",
                    "message":"操作成功",
                    "navTabId":"admin_info_cate_index",
                    "rel":"",
                    "reloadFlag":"1",
                    "callbackType":"closeCurrent",
                    "forwardUrl":"admin.php?mod=info_cate&do=index",
                    "confirmMsg":""
                }';
                die();
            } 
        }else{
            //DEBUG 新增信息
            $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
            if($is_submit){
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $cate_desc = isset($_POST['cate_desc']) ? $_POST['cate_desc'] : '';
                $info_catepid = isset($_POST['lookup_cate_info_cateid']) ? $_POST['lookup_cate_info_cateid'] : 0;
                $relate_info_cateid = isset($_POST['lookup_cate_relate_info_cateid']) ? $_POST['lookup_cate_relate_info_cateid'] : 0;
                $info_data = array(
                    "info_catepid" => $info_catepid,
                    "relate_info_cateid" => $relate_info_cateid,
                    "cate_desc" => $cate_desc,
                    "title" => $title,
                    "create_dateline" => TIMESTAMP
                );
                DB::insert('content_cate', $info_data);
                echo '{
                    "statusCode":"200",
                    "message":"操作成功",
                    "navTabId":"admin_info_cate_index",
                    "rel":"",
                    "reloadFlag":"1",
                    "callbackType":"closeCurrent",
                    "forwardUrl":"admin.php?mod=info_cate&do=index",
                    "confirmMsg":""
                }';
                die();
            }
        }

        include template('admin/info/info_cate_edit');
        break;
        
    case "lookup_cate";
        $wheresql ='';
        $cate_results_tmp = $cate_results = array();
        $sql = "SELECT info_cateid, title, info_catepid from ".DB::table('content_cate')." WHERE isdelete=0 ".$wheresql." ORDER BY info_cateid ASC";
        $cate_results_tmp = DB::fetch_all($sql);
        $cate_results_tree = dbarr2tree($cate_results_tmp);
        $relate = isset($_GET['relate']) ? $_GET['relate'] : 0;
        $tree2htmlul = tree2htmlul($cate_results_tree,$relate);
        /*
        foreach($cate_results_tmp AS $key => $value){
            if(empty($value['info_catepid'])){
                $cate_results[$value['info_cateid']] = $value;
            }else{
                if($cate_results[$value['info_catepid']]){
                    $cate_results[$value['info_catepid']]['sub_cate'][$value['info_cateid']] = $value;
                }else{
                    foreach($cate_results AS $key_sub => $value_sub){
                        if($value_sub['sub_cate']){
                            foreach($value_sub['sub_cate'] AS $key_sub_sub => $value_sub_sub){
                                if($key_sub_sub==$value['info_catepid']){
                                    $cate_results[$key_sub]['sub_cate'][$key_sub_sub]['sub_cate'][$value['info_cateid']] = $value;
                                }
                            }
                        }
                    }
                }
            }
        }
        */
        include template('admin/info/info_cate_tree');
        break;
    
    case "cate_ajax":
        //DEBUG 查询并返回信息链接
        $page_array = array();
        $sqlcount = $sql = $wheresql = $title_keyword = $pageNum = $numPerPage = '';
        $title_keyword = isset($_REQUEST['title_keyword']) ? $_REQUEST['title_keyword'] : '';
        $info_cateid = isset($_REQUEST['info_cateid']) ? $_REQUEST['info_cateid'] : '';
        $pageNum = isset($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
        $numPerPage = isset($_REQUEST['numPerPage']) ? $_REQUEST['numPerPage'] : 11;
        $pagestart = ($pageNum - 1) * $numPerPage;
        if($title_keyword){
            $wheresql .= " AND title LIKE '%".$title_keyword."%'";
        }
        if($info_cateid){
            $wheresql .= " AND info_catepid = '".$info_cateid."'";
        }
        $sqlcount = "SELECT count(*) from ".DB::table('content_cate')." WHERE isdelete=0 ".$wheresql;
        $pagetotal = DB::result_first($sqlcount);
        $sql = "SELECT info_cateid,info_catepid,title from ".DB::table('content_cate')." WHERE isdelete=0 ".$wheresql." ORDER BY info_cateid DESC LIMIT ".$pagestart.",".$numPerPage;
        $page_array = DB::fetch_all($sql);
        include template('admin/info/info_cate');
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
            $wheresql .= " AND title LIKE '%".$title_keyword."%'";
        }
        $sqlcount = "SELECT count(*) from ".DB::table('content_cate')." WHERE isdelete=0 ".$wheresql;
        $pagetotal = DB::result_first($sqlcount);
        $sql = "SELECT info_cateid,info_catepid,title from ".DB::table('content_cate')." WHERE isdelete=0 ".$wheresql." ORDER BY info_cateid DESC LIMIT ".$pagestart.",".$numPerPage;
        $page_array = DB::fetch_all($sql);
        
        //DEBUG 获取分类树形菜单 START
        $cate_results_tmp = $cate_results = array();
        $sql = "SELECT info_cateid, title, info_catepid from ".DB::table('content_cate')." WHERE isdelete=0 AND info_catepid = 0 ORDER BY info_cateid ASC";
        $cate_results_tmp = DB::fetch_all($sql);
        $cate_results_tree = dbarr2tree($cate_results_tmp);
        $tree2htmlul_infosearch = tree2htmlul_catesearch($cate_results_tree,'tree treeFolder');
        //DEBUG 获取分类树形菜单 END
        
        include template('admin/info/info_cate_multi');
        break;
}
?>