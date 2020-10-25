<?php
namespace app\index\controller;

use app\common\controller\IndexBase;


class Image extends IndexBase
{
    /**
     * 剪裁图
     * @param unknown $picurl
     * @param unknown $opt
     * @return mixed|string
     */
    public function cutimg($picurl,$opt){
        $this->assign('picurl',$picurl);
        list($w,$h) = explode(':', $opt);
        $Ratio = '';
        if($w&&$h){
            $Ratio = number_format($w/$h,2);
        }
        $this->assign('opt',"{\"aspectRatio\":\"$Ratio\"}");
        return $this->fetch();
	}
	
	/**
	 * 获取QQ表情
	 * @return mixed|string
	 */
	public function face(){
		return $this->fetch();
	}
	
	/**
	 * 图片缩放
	 */
	public function zoom($url=''){
	    $this->assign('url',$url);
	    return $this->fetch();
	}

	/**
	 * 主要给生成海报使用.解决跨域图片的问题
	 * @param string $url
	 */
	public function headers($url=''){
		if($url==''){
			return ;
		}
		if(preg_match("/^\//",$url)){
		    $url = get_url(preg_match("/^\/index(\.php|)\//", $url)?$url:tempdir($url));
		}
		$img = file_get_contents($url)?:http_curl($url); die($img);
		if($img==''){
			return ;
		}
		if(strstr($img,'<title>302')){	//设置了302跳转
			preg_match('/href="([^"]+)"/',$img,$array);
			$img = http_curl($array[1]);
		}
		header("Content-Type: image/jpeg;text/html; charset=utf-8");
		echo $img;
		exit;
	}
}