<?php

/**
 * 信息管理
 * @author HumingXu E-mail:huming17@126.com
 */
switch ($do) {
    case "delete":
        $info_id = isset($_REQUEST['info_id']) ? $_REQUEST['info_id'] : '';
        if($info_id){
            //DEBUG 更新isdelete=1 字段作为删除
            //DB::delete("content", array("info_id" => $info_id), $limit=1);
            DB::update("content", array("isdelete" => 1), array("info_id" => $info_id));
            echo '{
                "statusCode":"200",
                "message":"操作成功",
                "navTabId":"admin_info_index",
                "rel":"",
                "callbackType":"forward",
                "forwardUrl":"admin.php?mod=info&action=index",
                "confirmMsg":""
            }';
        }
        break;
    
    case "add":
    case "edit":
        $name_var = $info_url = '';
        $info_id = isset($_REQUEST['info_id']) ? $_REQUEST['info_id'] : '';
		//DEBUG 获取域名并为远程抓取图片设置js规则
		$domain = getdomain();
		$match_domain = str_replace('.','\.',$domain);
        if($info_id){
            //DEBUG 编辑更新信息
            $info_sql = "SELECT * FROM ".DB::table('content')." WHERE info_id='".$info_id."' LIMIT 1";
            $info_result = DB::fetch_first($info_sql);
            $info_result['cate_title'] = get_title_by_info_cateid($info_result['info_cateid']);
            $is_submit = isset($_POST['is_submit']) ? $_POST['is_submit'] : 0;
            if($is_submit){
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $content = isset($_POST['content']) ? $_POST['content'] : '';
                $info_img = isset($_POST['info_img']) ? $_POST['info_img'] : '';
                $info_cateid = isset($_POST['lookup_cate_info_cateid']) ? $_POST['lookup_cate_info_cateid'] : 0;
                //DEBUG 新增首页焦点信息及焦点信息位置
                $isfrontpage = isset($_POST['isfrontpage']) ? $_POST['isfrontpage'] : '';
                $frontpage_order = isset($_POST['frontpage_order']) ? $_POST['frontpage_order'] : '';
                DB::update('content', 
                        array(
                            "info_cateid" => $info_cateid,
                            "title" => $title,
                            "content" => $content,
                            "info_img" => $info_img,
                            "isfrontpage" => $isfrontpage,
                            "frontpage_order" => $frontpage_order,
                            'modify_dateline' => TIMESTAMP
                        ),
                        "info_id='".$info_id."' LIMIT 1"
                );
                echo '{
                    "statusCode":"200",
                    "message":"操作成功",
                    "navTabId":"admin_info_index",
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
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $content = isset($_POST['content']) ? $_POST['content'] : '';
                $info_cateid = isset($_POST['lookup_cate_info_cateid']) ? $_POST['lookup_cate_info_cateid'] : 0;
                $info_img = isset($_POST['info_img']) ? $_POST['info_img'] : '';
                $info_data = array(
                    "info_cateid" => $info_cateid,
                    "title" => $title,
                    "content" => $content,
                    "info_img" => $info_img,
                    'create_dateline' => TIMESTAMP
                );
                DB::insert('content', $info_data);
                echo '{
                    "statusCode":"200",
                    "message":"操作成功",
                    "navTabId":"admin_info_index",
                    "rel":"",
                    "reloadFlag":"1",
                    "callbackType":"closeCurrent",
                    "forwardUrl":"",
                    "confirmMsg":""
                }';
                die();
            }
        }

        include template('admin/info/info_edit');
        break;
        
    case "info_ajax":
        //DEBUG 查询并返回信息链接
        $page_array = array();
        $sqlcount = $sql = $wheresql = $title_keyword = $pageNum = $numPerPage = '';
        $title_keyword = isset($_REQUEST['title_keyword']) ? $_REQUEST['title_keyword'] : '';
        $pageNum = isset($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
        $numPerPage = isset($_REQUEST['numPerPage']) ? $_REQUEST['numPerPage'] : 15;
        $info_cateid = isset($_REQUEST['info_cateid']) ? $_REQUEST['info_cateid'] : '';
        $pagestart = ($pageNum - 1) * $numPerPage;
        if($title_keyword){
            $wheresql .= " AND info.title LIKE '%".$title_keyword."%'";
        }
        if($info_cateid){
            $wheresql .= " AND info.info_cateid = '".$info_cateid."'";
        }
        $sqlcount = "SELECT count(*) from ".DB::table('content')." AS info WHERE info.isdelete=0 ".$wheresql;
        $pagetotal = DB::result_first($sqlcount);
        $sql = "SELECT info.info_id, info.info_cateid, info.title, cate.title AS cate_title from ".DB::table('content')." AS info LEFT JOIN ".DB::table('content_cate')." AS cate ON info.info_cateid = cate.info_cateid WHERE info.isdelete=0 ".$wheresql." ORDER BY info.info_id DESC LIMIT ".$pagestart.",".$numPerPage;
        $page_array = DB::fetch_all($sql);
        
        include template('admin/info/info');
        break;
        
    default:
        //DEBUG 查询并返回信息链接
        $page_array = array();
        $sqlcount = $sql = $wheresql = $title_keyword = $pageNum = $numPerPage = '';
        $title_keyword = isset($_REQUEST['title_keyword']) ? $_REQUEST['title_keyword'] : '';
        $pageNum = isset($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
        $numPerPage = isset($_REQUEST['numPerPage']) ? $_REQUEST['numPerPage'] : 15;
        $pagestart = ($pageNum - 1) * $numPerPage;
        if($title_keyword){
            $wheresql .= " AND info.title LIKE '%".$title_keyword."%'";
        }
        $sqlcount = "SELECT count(*) from ".DB::table('content')." AS info WHERE info.isdelete=0 ".$wheresql;
        $pagetotal = DB::result_first($sqlcount);
        $sql = "SELECT info.info_id, info.info_cateid, info.title, cate.title AS cate_title from ".DB::table('content')." AS info LEFT JOIN ".DB::table('content_cate')." AS cate ON info.info_cateid = cate.info_cateid WHERE info.isdelete=0 ".$wheresql." ORDER BY info.info_id DESC LIMIT ".$pagestart.",".$numPerPage;
        $page_array = DB::fetch_all($sql);
        //DEBUG 获取分类树形菜单 START
        $cate_results_tmp = $cate_results = array();
        $sql = "SELECT info_cateid, title, info_catepid from ".DB::table('content_cate')." WHERE isdelete=0 ORDER BY info_cateid ASC";
        $cate_results_tmp = DB::fetch_all($sql);
        $cate_results_tree = dbarr2tree($cate_results_tmp);
        $tree2htmlul_infosearch = tree2htmlul_infosearch($cate_results_tree,'tree treeFolder');
        //DEBUG 获取分类树形菜单 END
        include template('admin/info/index_info_multi');
        break;
}
?>