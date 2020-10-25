<?php
switch($do){
	case "update":
		allow_crossdomain();
        $return_array = array("errcode"=>1,"errmsg"=>"无操作数据","data"=>array());
        $tablename = isset($_REQUEST['tablename']) ? $_REQUEST['tablename']:'';
        $tabledata = isset($_REQUEST['tabledata']) ? $_REQUEST['tabledata']:'';
        $tabledata_num = count($tabledata);
        $i=0;
        if(!empty($tablename) && !empty($tabledata)){
	        foreach($tabledata AS $key => $value){
	        	$return = DB::update($tablename,$value['data'],$value['condition']);
	        	if($return){
	        		$return_array["data"][] = array("errcode"=>0,"errmsg"=>"操作成功","data"=>$return);
	        		$i++;	
	        	}else{
	        		$return_array["data"][] = array("errcode"=>1,"errmsg"=>"操作失败","data"=>$return);	
	        	}
	        }
	        if($tabledata_num==$i){
	        	$return_array["errcode"]=0;
	        	$return_array["errmsg"]="操作成功";
	        }else{
	        	$return_array["errcode"]=1;
	        	$return_array["errmsg"]="操作失败";
	        }
        }
        echo json_ext($return_array);
	break;
	
	case "select":
        //DEBUG 查询并返回信息链接
        allow_crossdomain();
        $return_array = array("errcode"=>0,"errmsg"=>"无操作数据","data"=>array());
        $page_array = array();
        $tablename = isset($_REQUEST['tablename']) ? $_REQUEST['tablename']:'';
        $condition = isset($_REQUEST['condition']) ? $_REQUEST['condition']:'';
        $page_info = isset($condition['page_info']) ? $condition['page_info'] : '';
        $search_field = isset($condition['search_field']) ? $condition['search_field'] : '';
        $orderby_field = isset($condition['orderby_field']) ? $condition['orderby_field'] : '';
		
		$page = 1;
		$perpage = 0;
		$limitsql = '';
        if($page_info){
	        $page = max(1, intval($page_info['page']));
	        $limit = $perpage = isset($page_info['perpage']) ? $page_info['perpage']:0;
	        $start=(($page-1) * $perpage);
	        $limitsql = DB::limit($start, $limit);
        }
        $wheresql = '';
		if($search_field){
			$wheresql = ' WHERE '.$search_field['sql'];
		}
		$orderbysql ='';
		if($orderby_field){
			$orderbysql = ' ORDER BY '.$orderby_field['sql']."";
		}

        $sqlcount = "SELECT count(*) FROM ".DB::table($tablename).$wheresql;
        $pagetotal = DB::result_first($sqlcount);
        $sql = "SELECT *, FROM_UNIXTIME( modify_dateline, '%Y-%m-%d' ) AS modify_dateline_format, FROM_UNIXTIME( create_dateline, '%Y-%m-%d' ) AS create_dateline_format FROM ".DB::table($tablename)." ".$wheresql.$orderbysql.$limitsql;
        $pagedata = DB::fetch_all($sql);
		if($pagetotal > 0){
			$return_array = array("errcode"=>0,"errmsg"=>"操作成功","data"=>array('pagetotal'=>$pagetotal,'pagedata'=>$pagedata));
		}
        echo json_ext($return_array);
        break;
}
?>