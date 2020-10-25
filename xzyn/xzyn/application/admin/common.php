<?php

/**
 * @Title: ajaxReturn
 * @Description: todo(ajax提交返回状态信息)
 * @param string $info
 * @param url $url
 * @param string $status
 * @author 戏中有你
 * @date 2018年1月17日
 * @throws
 */
function ajaxReturn($info='', $url='', $status='', $data = ''){
    if(!empty($url)){   //操作成功
        $result = array( 'info' => '操作成功', 'status' => 1, 'url' => $url, );
    }else{   //操作失败
        $result = array( 'info' => '操作失败', 'status' => 0, 'url' => '', );
    }
    if(!empty($info)){$result['info'] = $info;}
    if(!empty($status)){$result['status'] = $status;}
    if(!empty($data)){$result['data'] = $data;}
    echo json_encode($result);
    exit();
}

/**
 * @Title: deldir
 * @Description: todo(删除文件-清理缓存)
 * @param string $dir
 * @param string $folder
 * @return boolean
 * @author 戏中有你
 * @date 2018年1月17日
 * @throws
 */
function deldir($dir,$folder='n'){
    //删除当前文件夹下得文件（并不删除文件夹）：
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath,$folder);
            }
        }
    }
    closedir($dh);
    //删除当前文件夹
    if($folder=='y'){
        if(rmdir($dir)){
            return true;
        } else {
            return false;
        }
    }
}

/**
 * @Title: unset_array
 * @Description: todo(删除二维数组中的值)
 * @param string $str
 * @param array $arr
 * @return Array
 * @author 戏中有你
 * @date 2018年1月17日
 * @throws
 */
function unset_array($str,$arr){
    foreach ($arr as $key => $value){
        if ($value === $str){
            unset($arr[$key]);
        }
    }
    return $arr;
}

/**
 * @Title: search_url
 * @Description: todo(搜索的地址)
 * @param string $delparam
 * @return string|unknown
 * @author 戏中有你
 * @date 2018年1月17日
 * @throws
 */
function search_url($delparam){
    $url_path = '';
    $get = input('get.');
    if( isset($get[$delparam]) ){ unset($get[$delparam]); }
    if( isset($get['_pjax'])   ){ unset($get['_pjax']);   }
    if(!empty($get)){
        $paramStr = [];
        foreach ($get as $k=>$v){
            $paramStr[] = $k.'='.$v;
        }
        $paramStrs = implode('&', $paramStr);
        $url_path = $url_path.'?'.$paramStrs;
    }
    return $url_path;
}

/**
 * @Title: table_sort
 * @Description: todo(列表table排序)
 * @param string $param
 * @return string
 * @author 戏中有你
 * @date 2018年1月17日
 * @throws
 */
function table_sort($param){
    $url_path = '';
    $faStr = 'fa-sort';
    $get = input('get.');
    if( isset($get['_pjax']) ){ unset($get['_pjax']); }

    if( isset($get['_sort']) ){   //判断是否存在排序字段
        $sortArr = explode(',', $get['_sort']);
        if ( $sortArr[0] == $param ){   //当前排序
            if ($sortArr[1] == 'asc'){
                $faStr = 'fa-sort-asc';
                $sort = 'desc';
            }elseif ($sortArr[1] == 'desc'){
                $faStr = 'fa-sort-desc';
                $sort = 'asc';
            }
            $get['_sort'] = $param.','.$sort;
        }else{   //非当前排序
            $get['_sort'] = $param.',asc';
        }
    }else{
        $get['_sort'] = $param.',asc';
    }
    $paramStr = [];
    foreach ($get as $k=>$v){
        $paramStr[] = $k.'='.$v;
    }
    $paramStrs = implode('&', $paramStr);
    $url_path = $url_path.'?'.$paramStrs;
    return "<a class=\"fa ".$faStr."\" href=\"".$url_path."\"></a>";
}

/**
 * @Title: authcheck
 * @Description: todo(权限节点判断)
 * @param string $rule
 * @param int $uid
 * @param string $relation
 * @param string $t
 * @param string $f
 * @return string
 * @author 戏中有你
 * @date 2018年1月17日
 * @throws
 */
function authcheck($rule, $uid, $relation='or', $t='', $f='noauth'){
    $auth = new \expand\Auth();
	$check = $auth->check($rule, $uid, $type=1, $mode='url',$relation);
    if( $check ){
        $result = $t;
    }else{
        $result = $f;
    }
    return $result;
}

