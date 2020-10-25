<?php
namespace plugins\weixin\libs\keyword;

use plugins\weixin\index\Api;

class Post_news extends Api
{
    public function run(){
    }
    
    /*
    private function post_hy_news($wxurl){
        global $db,$pre,$timestamp,$lfjuid,$lfjid,$user_appId,$webdb,$groupdb,$lfjdb;
        
        $ts = $db->get_one("SELECT * FROM {$pre}hy_wap WHERE uid='$lfjuid' ORDER BY id ASC LIMIT 1");
        $pageid = $ts[id];
        
        if(!$pageid){
            return ;
        }
        
        require_once(ROOT_PATH."hynews/inc/function.php");
        
        
        //$array = get_weixin_article($wxurl,$pageid);
        
        $postdb[content] = $array[content];
        $postdb[title] = $array[title];
        $postdb[picurl] = $array[picurl];
        
        $yz = ($lfjdb[news_num]+$groupdb[postHyNewsNum]>0) ? 1 : 0;
        
        if(!$postdb[title] || !$postdb[content]){
            send_wx_msg($user_appId,"很抱歉，发布失败，有可能是网络故障，请重新发布试试！");
        }
        
        //if($rs=$db->get_one("SELECT * FROM `{$pre}hynews_content` WHERE uid='$lfjuid' ORDER BY id DESC LIMIT 1")){
            if($rs[title]==$postdb[title]){
                $id=$rs[id];
                $content="请不要重复发布文章，已经发表过了
                
                $postdb[title]
                
                <a href=\"$webdb[www_url]/hynews/member/wappost.php?job=edit&fid=&id=$id\">修改</a>               <a href=\"$webdb[www_url]/hynews/wapbencandy.php?id=$id\">预览转发</a>
                ";
                send_wx_msg($user_appId,$content);
                exit;
            }
        //}
        
        //$db->query("INSERT INTO `{$pre}hynews_content` (`fid` , `title` , `picurl` , `content` ,`posttime` ,`uid` , `username` ,`yz`, `pageid` ) VALUES ('$fid', '$postdb[title]', '$postdb[picurl]', '$postdb[content]', '$timestamp','$lfjuid', '$lfjid','$yz','$pageid');");
        //$id=$db->insert_id();
        if($id){
            $content="内容发布成功
            
            $postdb[title]
            
            <a href=\"$webdb[www_url]/hynews/member/wappost.php?job=edit&fid=&id=$id\">修改</a>               <a href=\"$webdb[www_url]/hynews/wapbencandy.php?id=$id\">预览转发</a>
            ";
            
            //$db->query("UPDATE `{$pre}memberdata` SET news_num=news_num-1 WHERE uid='$lfjuid'");
            
            send_wx_msg($user_appId,$content);
        }
    }*/
    
}