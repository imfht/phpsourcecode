<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use expand\Auth;
use think\facade\Env;
use think\facade\Request;

//格式化打印函数
function p($array) {
	dump ( $array, 1, '<pre style=font-size:16px;color:#DB00DE;font-family:consola;>', 0 );
}

///* 提取所有图片 */
function getImgs($content,$order='all'){
	$pattern="/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
	preg_match_all($pattern,$content,$match);
	if(isset($match[1])&&!empty($match[1])){
		if($order==='all'){
			return $match[1];
		}
		if(is_numeric($order)&&isset($match[1][$order])){
			return $match[1][$order];
		}
	}
	return '';
}

require_once Env::get('VENDOR_PATH') . 'qiniu_php_sdk/autoload.php';

function delimg($imgurl = ''){	//删除图片
	$qiniu_yuming = confv('qiniu_yuming','up');
    $qiniu_AccessKey = confv('qiniu_AccessKey','up');
    $qiniu_SecretKey = confv('qiniu_SecretKey','up');
    $qiniu_bucket = confv('qiniu_bucket','up');
	$auth = new \Qiniu\Auth($qiniu_AccessKey, $qiniu_SecretKey);
	$config = new \Qiniu\Config();
	$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
	$xzyn_yuming = Request::domain();
	if( !empty($imgurl) ){
		if( is_array($imgurl) ){
			foreach ($imgurl as $k => $v) {
	    		if( strchr($v,$qiniu_yuming) ){		//删除在七牛的图片
	    			$imgurl_arr = explode($qiniu_yuming.'/',$v);
					$imgurls = trim($imgurl_arr[1]);	//移除两边空白和其它字符
					$err = $bucketManager->delete($qiniu_bucket, $imgurls);
				}else if( strchr($v,$xzyn_yuming) ){
					$imgurl_arr = explode($xzyn_yuming,$v);
					$imgurls = trim($imgurl_arr[1]);	//移除两边空白和其它字符
					if(!empty($imgurls) && $imgurls != '/static/common/img/logo.jpg'){
						if ( file_exists(WEB_PATH.$imgurls) ){
		        			unlink(WEB_PATH.$imgurls);	//删除图片
						}
					}
				}else{
					if(!empty($v) && $v != '/static/common/img/logo.jpg'){
						if ( file_exists(WEB_PATH.$v) ){
		        			unlink(WEB_PATH.$v);	//删除图片
						}
					}
				}
	    	}
		}else{
			if( strchr($imgurl,$qiniu_yuming) ){		//删除在七牛的图片
    			$imgurl_arr = explode($qiniu_yuming.'/',$imgurl);
				$imgurls = trim($imgurl_arr[1]);	//移除两边空白和其它字符
				$err = $bucketManager->delete($qiniu_bucket, $imgurls);
			}else if( strchr($imgurl,$xzyn_yuming) ){
				$imgurl_arr = explode($xzyn_yuming,$imgurl);
				$imgurls = trim($imgurl_arr[1]);	//移除两边空白和其它字符
				if(!empty($imgurls) && $imgurls != '/static/common/img/logo.jpg'){
					if ( file_exists(WEB_PATH.$imgurls) ){
	        			unlink(WEB_PATH.$imgurls);	//删除图片
					}
				}
			}else{
				if(!empty($imgurl) && $imgurl != '/static/common/img/logo.jpg'){
					if ( file_exists(WEB_PATH.$imgurl) ){
	        			unlink(WEB_PATH.$imgurl);	//删除图片
					}
				}
			}
		}
	}
	return true;
}


/**
 * @Title: confv
 * @Description: todo(获取配置值)
 * @param string $k
 * @param string $type
 * @return string
 * @author 戏中有你
 * @date 2018年1月16日
 * @throws
 */
function confv($k, $type = 'web'){
    $config = new app\common\model\Config;
    return $config->confv($k, $type);
}

/**
 * 模版带权限的按钮
 * @param string $url URL表达式，格式：'[分组/模块/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string $title  标题
 * @param string $mini  是否异步加载
 * @param string $class A标签样式
 * @param string|array  $vars 传入的参数，支持数组和字符串
 * @return string
 */
function BTN($url = '', $vars = '', $title = '', $class = "", $icon = '', $data = '', $mini = "") {
	$uid = session('userId');
    $auth = new Auth();
    if (!$auth->check($url, $uid, $type=1, $mode='url',$relation='or')){
        return '';
    }else{
    	$url = url($url, $vars);
    }
    $m = $c = $i = '';
    if (!empty($class)) {
        $c = ' class="' . $class . ' " ';
    }
    if (!empty($icon)) {
        $icon = '<i class="fa ' . $icon . ' "></i> ';
    }
    if (!empty($data)) {
        $data = $data;
    }
    if (empty($mini)) {
        return '<a href="' . $url . '" ' . $c . $data . ' >'. $icon . $title . '</a>';
    }else{
		return '<a href="javascript:void(0);" ' . $c . $data . ' >'. $icon . $title . '</a>';
    }

}

//随机生成颜色代码
function _color() {
  	$str='0123456789ABCD';
    $estr='#';
    $len=strlen($str);
    for($i=1;$i<=6;$i++)
    {
        $num=rand(0,$len-1);
        $estr=$estr.$str[$num];
    }
    return $estr;
}

//时间格式化  (多久前)
function time_line($time) {

    $t = time() - $time;
    $mon = (int) ($t / (86400 * 30));
    if ($mon >= 1) {
        return '一个月前';
    }
    $day = (int) ($t / 86400);
    if ($day >= 1) {
        return $day . '天前';
    }
    $h = (int) ($t / 3600);
    if ($h >= 1) {
        return $h . '小时前';
    }
    $min = (int) ($t / 60);
    if ($min >= 1) {
        return $min . '分前';
    }
    return '刚刚';
}

//时间格式化 (多久后)
function time_lines($time) {
	 $today  =  strtotime(date('Y-m-d')); //今天零点
      $here   =  (int)(($time - $today)/86400) ;
	  if($here==1){
		  return '明天';
	  }
	  if($here==2) {
		  return '后天';
	  }
	  if($here>=3 && $here<7){
		  return $here.'天后';
	  }
	  if($here>=7 && $here<30){
		  return '一周后';
	  }
	  if($here>=30 && $here<365){
		  return '一个月后';
	  }
	  if($here>=365){
		  $r = (int)($here/365).'年后';
		  return   $r;
	  }
	 return '今天';
}

//最新回复 10条
function newReplyList($num) {
	$arc_reply = new \app\common\model\ArchiveReply;
	$data = $arc_reply->newReplyList($num);
	return $data;
}

/**
 * @Title: auto_description
 * @Description: todo(自动获取内容的内容简介)
 * @return string
 * @author 戏中有你
 * @date 2018年1月16日
 * @throws
 */
function auto_description($d, $c){
    if( empty($d) ){
        if( !empty($c) ){
            $c = trimall(strip_tags(htmlspecialchars_decode($c)));   //转换标签-去掉HTML标签
            $c = csubstr($c, 200, '', 0, true);
            $result = $c;
        }else{
            $result = '';
        }
    }else{
        $result = $d;
    }
    return $result;
}

/**
 * @Title: trimall
 * @Description: todo(清除字符串中的空格和换行)
 * @param string $str
 * @return string
 * @author 戏中有你
 * @date 2018年1月16日
 * @throws
 */
function trimall($str){
    $qian=array(" ","　","\t","\n","\r");
    $hou=array("","","","","");
    return str_replace($qian,$hou,$str);
}

/**
 * @Title: csubstr
 * @Description: todo(中文字符串截取长度)
 * @param string $str [内容]
 * @param int $length [长度]
 * @param string $charset [编码]
 * @param int $start [开始位置]
 * @param boolean $suffix [结尾的....]
 * @return string
 * @author 戏中有你
 * @date 2018年1月16日
 * @throws
 */
function csubstr($str, $length, $charset="", $start=0, $suffix=true) {
    if (empty($charset))
        $charset = "utf-8";

        if (function_exists("mb_substr")) {
            if (mb_strlen($str, $charset) <= $length)
                return $str;
                $slice = mb_substr($str, $start, $length, $charset);
        }else {
            $re['utf-8'] = "/[\x01-\x7f]¦[\xc2-\xdf][\x80-\xbf]¦[\xe0-\xef][\x80-\xbf]{2}¦[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]¦[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]¦[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]¦[\x81-\xfe]([\x40-\x7e]¦\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            if (count($match[0]) <= $length)
                return $str;
            $slice = join("", array_slice($match[0], $start, $length));
        }
        if ($suffix)
            return $slice . "...";
        return $slice;
}

/**
   * @Title: arclist
   * @Description: todo(查询栏目下的文章)
   * @param int $typeid 栏目ID（当前栏目下的所有[无限级]栏目ID）
   * @param int $limit 查询数量
   * @param string $flag 推荐[c] 置顶[a] 头条[h] 滚动[s] 图片[p] 跳转[j]
   * @param string $order 排序
   * @return array
   * @author 戏中有你
   * @date 2018年2月8日
   * @throws
 */
function arclist($typeid='', $limit='', $flag='', $order='id DESC'){
    $archive = new \app\common\model\Archive();
    return $archive->arclist($typeid, $limit, $flag, $order);
}

// 二维数组按照指定键排序（正序或倒叙均可）
function array_sort( $array, $keys, $type='asc' ) {
    if( !isset( $array ) || !is_array( $array ) || empty( $array ) ) return '';
    if( !isset( $keys ) || trim( $keys ) == '' ) return '';
    if( !isset( $type ) || $type == '' || !in_array( strtolower( $type ), array( 'asc', 'desc' ) ) ) return '';

    $keysvalue  = [];
    foreach( $array as $key => $val ) {
        $val[ $keys ]   = str_replace( '-', '', $val[ $keys ] );
        $val[ $keys ]   = str_replace( ' ', '', $val[ $keys ] );
        $val[ $keys ]   = str_replace( ':', '', $val[ $keys ] );
        $keysvalue[]    = $val[ $keys ];
    }

    asort( $keysvalue ); //key值排序
    reset( $keysvalue ); //指针重新指向数组第一个
    foreach( $keysvalue as $key => $vals )
        $keysort[] = $key;

    $keysvalue  = [];
    $count      = count( $keysort );
    if( strtolower( $type ) != 'asc' ) {
        for( $i = $count - 1; $i >= 0; $i-- )
            $keysvalue[] = $array[ $keysort[ $i ] ];
    }else{
        for( $i = 0; $i < $count; $i++ )
            $keysvalue[] = $array[ $keysort[ $i ] ];
    }
    return $keysvalue;
}