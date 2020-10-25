<?php 
namespace plugins\weixin\libs\keyword;

use plugins\weixin\index\Api;

class Id_binding_yz extends Api
{
    public function run(){
    }
    
    protected function reply_keyword(){
        $content = $this->From_content;
        if(preg_match("/^注册/",$content)){
            //$username=substr($content,4);	//因为注册是四个字节，所以取之后的有效用户名
            //require_once(ROOT_PATH."inc/weixin/yznum.inc.php");
            //echo give_text("你的注册码是：$rand_num");
            exit;
        }elseif(preg_match("/^绑定/",$content)){
            //$username=substr($content,4);	//因为绑定是四个字节，所以取之后的有效用户名
            //require_once(ROOT_PATH."inc/weixin/yznum.inc.php");
            //echo give_text("你的验证码是：$rand_num");
            exit;
        }elseif(preg_match("/^md/",$content)){
            //$username=substr($content,4);	//因为绑定是四个字节，所以取之后的有效用户名
            //require_once(ROOT_PATH."inc/weixin/yznum.inc.php");
            // check_out_money($content);
            exit;
        }elseif(preg_match("/^http:\/\/mp.weixin.qq.com\//",$content)||preg_match("/toutiao.com/",$content)||preg_match("/^https:\/\/mp.weixin.qq.com\/",$content)||preg_match("/^http:\/\/www.wxyxpt.com\/hynews/",$content)){
            //post_hy_news($content);
            //exit;
        }elseif(preg_match("/^http/",$content)){
            //echo give_text("很抱歉，系统目前只支持抓取转发公众号的文章，网址为mp.weixin.qq.com开头的网址，你也可以从系统提供的精彩软文直接转发\r\n\r\n<a href=\"$webdb[www_url]/hynews/waplist.php\">精彩软文</a>            <a href=\"$webdb[www_url]/help/4.htm\">查看教程</a>");
            exit;
        }
    }
    
}