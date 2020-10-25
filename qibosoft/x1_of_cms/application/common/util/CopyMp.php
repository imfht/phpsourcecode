<?php

namespace app\common\util;
use think\Db;

class CopyMp
{
    public static function get_weixin_article($wxurl='',$fid=0,$ext_id=0){
        $www_url = request()->domain();
        if(strstr($wxurl,'toutiao.com')){
            return static::get_toutiao_article($wxurl);
        }
        if(strstr($wxurl,$www_url)){
            preg_match("/(\?|&)id=([0-9]+)/is",$wxurl,$array);
            $id = $array[2];
            $rs = Db::name(config('system_dirname').'_content1')->where('id',$id)->find();
            $postdb['title'] = ($rs['title']);
            $postdb['picurl'] = ($rs['picurl']);
            $postdb['content'] = ($rs['content']);
            return $postdb;
        }
        $content = file_get_contents($wxurl);
        if($content==''){
            $content=http_curl($wxurl);
        }
        
//         preg_match("/<title>(.*?)<\/title>
// /is",$content,$array);
        
//         if($array[1]==''){
//             preg_match("/<title>(.*?)<\/title>/is",$content,$array);
//         }
        preg_match('/var msg_title = ("|\')([^"\']+)("|\')/is',$content,$array);
        $postdb['title'] = $array[2];
        
        preg_match("/var msg_cdn_url = \"([^\"]+)\"/is",$content,$array);
        $postdb['picurl'] = addslashes($array[1]);
        
        preg_match("/id=\"js_content\">(.*?)<script nonce=/is",$content,$array);        
        $postdb['content'] = '<div>'.$array[1];
        if (empty($array[1])) {
            preg_match("/<div class=\"rich_media_content \" id=\"js_content\" style=\"visibility: hidden;\">(.*?)<script nonce=/is",$content,$array);
            $postdb['content'] = '<div>'.$array[1];
        }        

        if(strstr($postdb['content'],'v.qq.com/iframe/')||strstr($postdb['content'],'pages/video_player_tmpl')){
            $postdb['content'] = static::get_iframe_mv($postdb['content']);
        }
        
        //$postdb['title'] = addslashes($postdb['title']);
        $postdb['content'] = addslashes($postdb['content']);
        
        
        $postdb['content'] = str_replace('/0?wx_fmt=jpeg','/640?wx_fmt=jpeg&amp;tp=jpg&amp;wxfrom=5&amp;wx_lazy=1',$postdb['content']);
        $postdb['content'] = str_replace('/640?wx_fmt=jpeg','/640?wx_fmt=jpeg&amp;tp=jpg&amp;wxfrom=5&amp;wx_lazy=1',$postdb['content']);
        $postdb['content'] = str_replace('/0?wx_fmt=gif','/640?wx_fmt=gif&amp;tp=gif&amp;wxfrom=5&amp;wx_lazy=1',$postdb['content']);
        $postdb['content'] = str_replace('/640?wx_fmt=gif','/640?wx_fmt=gif&amp;tp=gif&amp;wxfrom=5&amp;wx_lazy=1',$postdb['content']);
        $postdb['content'] = str_replace('/0?wx_fmt=png','/640?wx_fmt=png&amp;tp=png&amp;wxfrom=5&amp;wx_lazy=1',$postdb['content']);
        $postdb['content'] = str_replace('/640?wx_fmt=png','/640?wx_fmt=png&amp;tp=png&amp;wxfrom=5&amp;wx_lazy=1',$postdb['content']);
        
        $postdb['content'] = str_replace('/640?tp=webp','/640?wx_fmt=png&amp;tp=png&amp;wxfrom=5&amp;wx_lazy=1',$postdb['content']);
        
        $postdb['content'] = str_replace('/mmbizgif?tp=jpg','/640?wx_fmt=png&amp;tp=png&amp;wxfrom=5&amp;wx_lazy=1',$postdb['content']);
        
        $postdb['content'] = str_replace('/640?\\"','/640?wx_fmt=jpeg&amp;tp=jpg&amp;wxfrom=5&amp;wx_lazy=1\\"',$postdb['content']);
        $postdb['content'] = str_replace('/0?\\"','/0?wx_fmt=jpeg&amp;tp=jpg&amp;wxfrom=5&amp;wx_lazy=1\\"',$postdb['content']);
        
        $postdb['content'] = str_replace('/s640?\\"','/0?wx_fmt=jpeg&amp;tp=jpg&amp;wxfrom=5&amp;wx_lazy=1\\"',$postdb['content']);
        
        $postdb['content'] = str_replace('/0\\"','/0?wx_fmt=jpeg&amp;tp=jpg&amp;wxfrom=5&amp;wx_lazy=1\\"',$postdb['content']);
        $postdb['content'] = str_replace('/640\\"','/640?wx_fmt=jpeg&amp;tp=jpg&amp;wxfrom=5&amp;wx_lazy=1\\"',$postdb['content']);
        
        $postdb['content'] = str_replace('/640.jpeg?\\"','/0?wx_fmt=jpeg&amp;tp=jpg&amp;wxfrom=5&amp;wx_lazy=1\\"',$postdb['content']);
        
        
        
        //$postdb['content'] = str_replace('https://','http://',$postdb['content']);
        //$postdb['content'] = str_replace(' data-src=',' src=',$postdb['content']);
        
        $postdb['content'] = str_replace(' data-src=\"http://mmbiz.',' src=\"http://mmbiz.',$postdb['content']);
        $postdb['content'] = str_replace(' data-src=\"https://mmbiz.',' src=\"http://mmbiz.',$postdb['content']);
        $postdb['content'] = str_replace(' src=\"https://mmbiz.',' src=\"http://mmbiz.',$postdb['content']);
        
        preg_match_all("/(http|https):\/\/([^ '\"<>]+)(\.gif|\.jpg|\.png|wx_lazy=1)/is",$postdb['content'],$array);
        $filedb=$array[0];
        foreach( $filedb AS $key=>$value){
            if( strstr($value,$www_url) ){
                continue;
            }
            $listdb[$value]=$value;
        }
        unset($filedb);
        foreach( $listdb AS $key=>$value){
            $postdb['content'] = str_replace($value,purl('weixin/mpimg/get',[],'index')."?id=$ext_id&fid=$fid&url=".urlencode($value),$postdb['content']);
        }
        //$postdb['content'] = str_replace(' data-src=',' src=',$postdb['content']);
        $postdb['content'] = stripslashes($postdb['content']);
        return $postdb;
    }
    
    protected static function get_iframe_mv($s){
        
        //preg_match("/<iframe([^>]+)data-src=\"([^\"]+)\"([^>]+)><\/iframe>/is",$s,$array);
        //preg_match("/vid=([^&\"]+)/is",$array[2],$array2);
        //$vid = $array2[1];
        
        //$code="<iframe class=\"video_iframe\" data-vidtype=\"1\" style=\"z-index: 1; width: 320px !important; height: 250px !important; overflow: hidden;\" height=\"250\" width=\"320\" frameborder=\"0\" data-src=\"https://v.qq.com/iframe/preview.html?vid={$vid}&amp;width=500&amp;height=375&amp;auto=0\" allowfullscreen=\"\" src=\"http://v.qq.com/iframe/player.html?vid={$vid}&amp;width=320&amp;height=250&amp;auto=0\" scrolling=\"no\"></iframe>";
        
        //$s = preg_replace("/<iframe([^>]+)data-src=\"([^\"]+)\"([^>]+)><\/iframe>/is",$code,$s);
        
        $s = preg_replace_callback("/<iframe([^>]+)data-src=\"([^\"]+)\"([^>]*)><\/iframe>/is",array(self,get_iframe_mv_id),$s);
        //$s = preg_replace("/<iframe([^>]+)data-src=\"([^\"]+)\"([^>]*)><\/iframe>/eis","get_iframe_mv_id('\\2')",$s);
        
        return $s;
    }
    
//     protected function get_iframe_mv_id($url){
//         preg_match("/vid=([^&\"]+)/is",$url,$array2);
//         $vid = $array2[1];
        
//         $code="<iframe class=\"video_iframe\" data-vidtype=\"1\" style=\"z-index: 1; width: 320px !important; height: 250px !important; overflow: hidden;\" height=\"250\" width=\"320\" frameborder=\"0\" data-src=\"https://v.qq.com/iframe/preview.html?vid={$vid}&amp;width=500&amp;height=375&amp;auto=0\" allowfullscreen=\"\" src=\"http://v.qq.com/iframe/player.html?vid={$vid}&amp;width=320&amp;height=250&amp;auto=0\" scrolling=\"no\"></iframe>";
//         return $code;
//     }
    
    protected static function get_iframe_mv_id($array=[]){
        //https://mp.weixin.qq.com/mp/readtemplate?t=pages/video_player_tmpl&amp;action=mpvideo&amp;auto=0&amp;vid=wxv_774069477334974465
        
        $url = $array[2];
        if (strstr($url,'pages/video_player_tmpl')) {
            return "<iframe class=\"video_iframe\" data-vidtype=\"1\" style=\"z-index: 1; width: 320px !important; height: 250px !important; overflow: hidden;\" height=\"250\" width=\"320\" frameborder=\"0\" data-src=\"{$url}\" allowfullscreen=\"\" src=\"{$url}\" scrolling=\"no\"></iframe>
                    <br><a href='{$url}'>若视频播放不了，请点击查看视频</a>";
        }
        preg_match("/vid=([^&\"]+)/is",$url,$array2);
        $vid = $array2[1];
        
        $code="<iframe class=\"video_iframe\" data-vidtype=\"1\" style=\"z-index: 1; width: 320px !important; height: 250px !important; overflow: hidden;\" height=\"250\" width=\"320\" frameborder=\"0\" data-src=\"https://v.qq.com/iframe/preview.html?vid={$vid}&amp;width=500&amp;height=375&amp;auto=0\" allowfullscreen=\"\" src=\"https://v.qq.com/iframe/player.html?vid={$vid}&amp;width=320&amp;height=250&amp;auto=0\" scrolling=\"no\"></iframe>";
        return $code;
    }
    
    protected static function get_toutiao_article($url){
        $content = file_get_contents($url);
        if($content==''){
            $content = http_curl($url);
        }
        
        preg_match("/<h1 class=\"article-title\">(.*?)<\/h1>/is",$content,$array);
        
        $postdb['title'] = $array[1];
        
        
        preg_match("/<div class=\"article-content\">(.*?)<\/div>([^><]+)<div class=\"article-actions\">/is",$content,$array);
        $postdb['content'] = $array[1];
        
        
        preg_match("/<img src=\"([^\"]+)\"([^><]+)>/is",$content,$array);
        
        $postdb['picurl'] = str_replace('/large/','/list/204x140/',$array[1]);
        
        $postdb['picurl'] = addslashes($postdb['picurl']);
        $postdb['title'] = addslashes($postdb['title']);
        $postdb['content'] = addslashes($postdb['content']);
        
        return $postdb;
    }
}
