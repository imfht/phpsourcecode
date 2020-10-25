<?php
use app\common\model\Arctype;
use app\common\model\Archive;
use app\common\model\Flink;
use app\common\model\Banner;

/**
 * @Title: ajaxReturn
 * @Description: todo(ajax提交返回状态信息)
 * @param string $info
 * @param url $url
 * @param string $status
 * @author 戏中有你
 * @date 2018年1月16日
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
    return json($result);
    exit();
}

/**
 * @Title: channeldata
 * @Description: todo(当前ID的平级栏目)
 * @param int $pid 上级栏目ID
 * @return array
 * @author 戏中有你
 * @date 2018年1月16日
 * @throws
 */
function channeldata($pid){
    $arctype = new Arctype();
    return $arctype->channeldata($pid);
}

/**
 * @Title: prenext
 * @Description: todo(上一篇、下一篇)
 * @param array $archiveArr 当前文档数组
 * @return string
 * @author 戏中有你
 * @date 2018年1月16日
 * @throws
 */
function prenext($archiveArr){
    $archive = new Archive();
    return $archive->prenext($archiveArr);
}

/**
 * @Title: click
 * @Description: todo(文档点击数+1)
 * @param array $archiveArr 当前文档数组
 * @author 戏中有你
 * @date 2018年1月16日
 * @throws
 */
function click($archiveArr){
    $archive = new Archive();
    $archive->click($archiveArr);
}


function flinks(){
    $flink = new Flink();
}

/**
 * @Title: banners
 * @Description: todo(banner模块数据)
 * @param int $mid
 * @param string $limit
 * @author 戏中有你
 * @date 2018年1月16日
 * @throws
 */
function banners($mid, $limit=''){
    $banner = new Banner();
    return $banner->banners($mid, $limit);
}

function tag(){

}