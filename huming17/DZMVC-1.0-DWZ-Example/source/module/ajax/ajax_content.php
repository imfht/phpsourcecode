<?php
switch($do){
	case "scroll_page":
            header('Content-Type: application/json');
            $delay = isset($_GET['delay']) ? $_GET['delay'] : 2;
            sleep($delay);
            $data = array();
            $image = array('txt', 'doc', 'xls', 'album', 'pdf', 'ppt', 'ufo');
            $count = count($image);
            //DEBUG 请求获取一页列表数据例子
            $page = max(1, intval($_GET['page']));
            $perpage = $limit = 6;
            $start=(($page-1) * $perpage);

            $cate_id = isset($_GET['id']) ? $_GET['id'] : '';

            $wheresql = '';
            if($cate_id){
                $wheresql = " AND info_cateid='".$cate_id."' ";
                //DEBUG 获取TITLE名称
                $cate_title = get_title_by_info_cateid($cate_id);
            }

            $sql_info = "SELECT * FROM ".DB::table('content')." WHERE isdelete=0 ".$wheresql." ORDER BY info_id DESC ".DB::limit($start, $limit);
            $sql_info_result = DB::fetch_all($sql_info);
            //$sql_total_rows = "SELECT count(*) FROM ".DB::table('content')." WHERE isdelete=0 ".$wheresql."";
            //$sql_total_rows_result = DB::result_first($sql_total_rows);
            //$multipage = multi($sql_total_rows_result, $perpage, $page, "index.php?mod=index&action=cate&id=".$cate_id);
            foreach($sql_info_result AS $key => $value){
                $title_img = get_first_imgpath_from_html($value['content']);
                if($title_img){
                   $sql_info_result[$key]['title_img'] = $title_img;
                }else{
                   $sql_info_result[$key]['title_img'] = 'src="./template/gmu/static/js/gmu/assets/img/slider.png"';
                }
                $sql_info_result[$key]['content_desc'] = cutstr(strip_tags($value['content']), 24, '...');
                if($value['create_dateline']){
                    $sql_info_result[$key]['create_dateline_format'] = date('Y-m-d',$value['create_dateline']);
                }else{
                    $sql_info_result[$key]['create_dateline_format'] = '';
                }
                $data[] = array(
                    'html'=>'<li data-highlight="ui-list-hover"><a href="javascript:info_detail('.$sql_info_result[$key]['info_id'].')" target="_self">
                                <img '.$sql_info_result[$key]['title_img'].' style="width:72px" />
                                <dl>
                                    <dt>'.$sql_info_result[$key]['title'].'</dt>
                                    <dd class="content">'.$sql_info_result[$key]['content_desc'].'</dd>
                                    <dd class="source">'.$sql_info_result[$key]['create_dateline_format'].'</dd>
                                </dl>
                            </a></li>'
                );
            }
            echo json_encode($data);
	break;

        case "info_detail";
            header('Content-Type: application/json');
            //$delay = isset($_GET['delay']) ? $_GET['delay'] : 0;
            //sleep($delay);
            $info_id = isset($_GET['info_id']) ? $_GET['info_id'] : '';
            if($info_id){
                $sql_info = "SELECT * FROM ".DB::table('content')." WHERE info_id='".$info_id."' LIMIT 1";
                $sql_info_result = DB::fetch_first($sql_info);
            }
            $return_data['title'] = $sql_info_result['title'];
            $return_data['content'] = $sql_info_result['content'];
            echo json_encode($return_data);
            break;
}
?>