<?php
use Think\Model;
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
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
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
function is_mobile() {
   
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
    $mobile_browser = '0';  
    if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))  
        $mobile_browser++;  
    if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_PROFILE']))  
        $mobile_browser++;  
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));  
    $mobile_agents = array(  
    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
    'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
    'wapr','webc','winw','winw','xda','xda-'
    );  
    if(in_array($mobile_ua, $mobile_agents))  
        $mobile_browser++;  
    if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)  
        $mobile_browser++;  
    // Pre-final check to reset everything if the user is on Windows  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)  
        $mobile_browser=0;  
    // But WP7 is also Windows, with a slightly different characteristic  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)  
        $mobile_browser++;  
    if($mobile_browser>0)  
        return true;  
    else
        return false;
}
function get_tag_url($article_id = 0,$content = ''){
    if($article_id ==0){
        return ;
    }
    $where['object_id'] = $article_id;
    $where['type']      = 'article';
    $ids = D('TagRelation')->field('tag_id')->where($where)->select();
   
    foreach ($ids as $row){
        $tag_name = get_tag_name($row['tag_id']);
        $href     = U('Article/tag',array('id'=>$row['tag_id']));
        $new_tag  = "<a href='".$href."' class='content_tag'>".$tag_name."</a>";
       
        $content = str_replace($tag_name, $new_tag, $content);
    }
    return $content;
}
/**
 * 插入广告
 * @param number $template 模板id
 * 1 首页
   2 商品详情页
   3 文章详情页
   4 登录页
   5 注册页
 * @param number $positon 广告位置id或标识
 * @param number $object_id 对象id，如文章id，知名对象id后，则该广告只在该对象所在页面显示
 */
function ad($template = 0, $positon = 0, $object_id = 0 ){
    if($template == 0 || empty($positon)){
        return ;
    }
    if(is_string($positon)){
        $where['name'] = $positon;
        $AdPositionModel = D('AdPosition');
        $r = $AdPositionModel->field('id')->where($where)->find();   
        if($r){
            $positon = $r['id'];
        }else {
            return ;
        }
    }
    $where['template'] = $template;
    $where['positon'] = $positon;
    if($object_id > 0){
        $where['object_id'] = $object_id;
    }

    $AdModel = D('Ad');
    $ad = $AdModel->where($where)->find();
    $style ='';
    if($ad){
        switch ($ad['type']){
            case 1:
                if($ad['width']&&$ad['height']){
                    $style=" style='width:".$ad['width'].";height:".$ad['height'].";' ";
                }
                $img = "<img src='".get_image_url($ad['pic_url'])."' ".$style.">";
                $div = "<div class='ad'>".$img."</div>";
                $html="<a href='".get_url($ad['link'])."' target='_blank'>".$div."</a>";
                break;
            case 2:
                $html = $ad['code'];
              
                break;
            case 3:
                $html="<a href='".get_url($ad['link'])."' target='_blank'>".$ad['code']."</a>";
                break;
        }
        
    }
    return  $html;
}

function create_rand_num($leng){
    $rand_str = "";
    $str="0123456789";
    for($i=0;$i<$leng;$i++){
        $rand_str .= $str[mt_rand(0, strlen($str)-1)];
    }
    return $rand_str;
}
function get_tk_goods_id($str,$get_pattern=false){
    $pattern = "/id=(\d{10,13})/is";
    if($get_pattern == true) return $pattern;
    if(!$str) return false;
    $str = trim($str);

    if(preg_match($pattern,$str,$arr)){
        return $arr[1];
    }elseif(preg_match("/(\d{10,13})/is",$str,$arr)){
        return $arr[1];
    }elseif(is_numeric($str)){
        return $str;
    }else{
        return false;
    }
}
