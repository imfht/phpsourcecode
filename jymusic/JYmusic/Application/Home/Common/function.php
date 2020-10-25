<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}

/**
 *获取评论
 *@param  string $id  指定歌曲评论
 *
*/

function get_sons_comment($id,$num=null) {
	if (isset($id)) $map['infos_id'] = $id;	
	$map['model_id'] = 1;
	$num = isset($num)? $num : 3 ;
	$list= D('Comment')->where($map)->field('user,content')->limit($num)->select();
	return $list;
}



/**
 *获取曲风
 *
*/
function get_genre($num=null) {
	//if (isset($id)) $map['infos_id'] = $id;	
	//$num = isset($num)? $num : 10 ;
	$list= D('Genre')->field('name,id,pid')->order('id desc')->select();
	return list_to_tree($list);
}

/**
 *获取专辑的所有歌曲
 *
*/
function get_Album_songs($id=null) {
	if (!empty($id)){
		$map['album_id'] = $id;
		$list= D('Songs')->where($map)->field('name,id,artist_id,artist_name,genre_name,genre_id')->order('id desc')->select();
		return $list;
	}else{
		return false;
	}
}

/*获取每日更新数量*/
function updated_daily() {
	$count = array();
	$time = strtotime(date("Y-m-d"));//获取0点的时间戳
	$genre =  M('Genre')->where(array('pid'=>0))->select();//获取歌曲总数
	$map['add_time'] = array('gt',$time);
	$count['songs']  =  M('Songs')->where($map)->count();//获取歌曲总数
	//dump($count);
}


